<?php

/**
 * WeChat Mini-Program user login.
 * 
 * @author Hailong Zhao <hailongzh@hotmail.com>
 * 
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider as BaseUserProvider;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface as SyliusUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserMiniProgramInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zshwag\Bundle\WechatBundle\Security\Authentication\Response\CodeToSessionResponse;

/**
 * Loading and ad-hoc creation of a user by an Mini-Program sign-in provider account.
 */
class MiniProgramProvider extends BaseUserProvider
{
    /**
     * @var FactoryInterface
     */
    private $miniProgramFactory;

    /**
     * @var RepositoryInterface
     */
    private $miniProgramRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var ObjectManager
     */
    private $userManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param string $supportedUserClass
     * @param FactoryInterface $customerFactory
     * @param FactoryInterface $userFactory
     * @param UserRepositoryInterface $userRepository
     * @param FactoryInterface $oauthFactory
     * @param RepositoryInterface $oauthRepository
     * @param ObjectManager $userManager
     * @param CanonicalizerInterface $canonicalizer
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        string $supportedUserClass,
        FactoryInterface $customerFactory,
        FactoryInterface $userFactory,
        UserRepositoryInterface $userRepository,
        FactoryInterface $miniProgramFactory,
        RepositoryInterface $miniProgramRepository,
        ObjectManager $userManager,
        CanonicalizerInterface $canonicalizer,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($supportedUserClass, $userRepository, $canonicalizer);

        $this->customerFactory = $customerFactory;
        $this->miniProgramFactory = $miniProgramFactory;
        $this->miniProgramRepository = $miniProgramRepository;
        $this->userFactory = $userFactory;
        $this->userManager = $userManager;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Load user with WeChat Open ID.
     *
     * @param CodeToSessionResponse $response
     *
     * @return UserInterface
     */
    public function loadUserByCodeToSessionResponse(CodeToSessionResponse $response): UserInterface
    {
        $wxUser = $this->miniProgramRepository->findOneBy([
            'openId' => $response->getOpenId(),
        ]);

        if ($wxUser instanceof UserMiniProgramInterface) {
            return $wxUser->getUser();
        }

        if (null !== $response->getEmail()) {
            $user = $this->userRepository->findOneByEmail($response->getEmail());
            if (null !== $user) {
                return $this->updateUserByCodeToSessionResponse($user, $response);
            }
        }

        return $this->createUserByCodeToSessionResponse($response);
    }

    /**
     * Ad-hoc creation of user.
     *
     * @param UserResponseInterface $response
     *
     * @return SyliusUserInterface
     */
    private function createUserByCodeToSessionResponse(CodeToSessionResponse $response): SyliusUserInterface
    {
        /** @var SyliusUserInterface $user */
        $user = $this->userFactory->createNew();

        $canonicalEmail = $this->canonicalizer->canonicalize($response->getEmail());

        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $canonicalEmail]);

        if (null === $customer) {
            $customer = $this->customerFactory->createNew();
        }

        $user->setCustomer($customer);

        // set default values taken from OAuth sign-in provider account
        if (null !== $email = $response->getEmail()) {
            $customer->setEmail($email);
        }
/*
        if (null !== $name = $response->getFirstName()) {
            $customer->setFirstName($name);
        } elseif (null !== $realName = $response->getRealName()) {
            $customer->setFirstName($realName);
        }

        if (null !== $lastName = $response->getLastName()) {
            $customer->setLastName($lastName);
        }

        if (!$user->getUsername()) {
            $user->setUsername($response->getEmail() ?: $response->getNickname());
        }
*/
        // set random password to prevent issue with not nullable field & potential security hole
        $user->setPlainPassword(substr(sha1($response->getJsCode()), 0, 10));

        $user->setEnabled(true);

        return $this->updateUserByCodeToSessionResponse($user, $response);
    }

    /**
     * Attach OAuth sign-in provider account to existing user.
     *
     * @param UserInterface $user
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     */
    private function updateUserByCodeToSessionResponse(UserInterface $user, CodeToSessionResponse $response): UserInterface
    {
        $wxUser = $this->miniProgramFactory->createNew();
        $wxUser->setOpenId($response->getOpenId());
        $wxUser->setUnionId($response->getUnionid());
        $wxUser->setSessionKey($response->getSessionKey());
        $wxUser->setJsCode($response->getJsCode());
        $wxUser->setLastLogin(time());
        $wxUser->setUser($user);

        /** @var SyliusUserInterface $user */
        $user->addMiniProgramAccount($wxUser);

        $this->userManager->persist($user);
        $this->userManager->flush();

        return $user;
    }
}
