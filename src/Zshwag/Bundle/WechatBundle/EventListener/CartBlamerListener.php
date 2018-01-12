<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Zshwag\Bundle\WechatBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;

final class CartBlamerListener
{
    /**
     * @var ObjectManager
     */
    private $cartManager;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param ObjectManager $cartManager
     * @param CartContextInterface $cartContext
     */
    public function __construct(ObjectManager $cartManager, CartContextInterface $cartContext)
    {
        $this->cartManager = $cartManager;
        $this->cartContext = $cartContext;
    }

    /**
     * @param UserEvent $userEvent
     */
    public function onImplicitLogin(UserEvent $userEvent): void
    {
        $user = $userEvent->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->blame($user);
    }

    /**
     * @param InteractiveLoginEvent $interactiveLoginEvent
     */
    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        if (!$interactiveLoginEvent->getAuthenticationToken() instanceof JWTUserToken) {
            return;
        }

        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->blame($user, $interactiveLoginEvent);
    }

    /**
     * @param ShopUserInterface $user
     * @param InteractiveLoginEvent $event
     */
    private function blame(ShopUserInterface $user, InteractiveLoginEvent $event): void
    {
        $cart = $this->getCart();
        if (null === $cart) {
            return;
        }

        if (null === $cart->getTokenValue()) {
            $token = $event->getAuthenticationToken();

            // Generate a hash
            $tokenValue = md5($user->getId() . substr($token->getCredentials(), -4) . time());

            $cart->setTokenValue($tokenValue);
            $this->cartManager->persist($cart);
            $this->cartManager->flush();
        }

        $event->getRequest()->attributes->set('token', $cart->getTokenValue());
    }

    /**
     * @return OrderInterface|null
     *
     * @throws UnexpectedTypeException
     */
    private function getCart(): ?OrderInterface
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return null;
        }

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        return $cart;
    }
}
