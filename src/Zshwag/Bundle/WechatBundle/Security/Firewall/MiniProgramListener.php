<?php

namespace Zshwag\Bundle\WechatBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Zshwag\Bundle\WechatBundle\Security\Authentication\Token\MiniProgramUserToken;

/**
 * WeChat Mini-Program user login.
 * 
 * @author Hailong Zhao <hailongzh@hotmail.com>
 * 
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
 */
class MiniProgramListener extends AbstractAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
        if (!$request->headers->has('X-WX-Code') ||
            !$request->headers->has('X-WX-Encrypted-Data') ||
            !$request->headers->has('X-WX-IV')) {
            throw new AuthenticationException('No authentication code in the request.');
        }

        $token = new MiniProgramUserToken(
            $request->headers->get('X-WX-Code'),
            $request->headers->get('X-WX-Encrypted-Data'),
            $request->headers->get('X-WX-IV')
        );

        return $this->authenticationManager->authenticate($token);
    }
}
