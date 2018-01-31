<?php

namespace Zshwag\Bundle\WechatBundle\EventListener;

use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['code'] = 0;
        $data['data'] = array(
            'skey' => $data['token'],
            'userinfo' => $user->getMiniProgramUserInfo(),
        );
        unset($data['token']);

        $event->setData($data);
    }
}
