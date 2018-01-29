<?php

namespace Zshwag\Bundle\WechatBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use EasyWeChat\Kernel\ServiceContainer;
use Zshwag\Bundle\WechatBundle\Security\Authentication\Response\CodeToSessionResponse;
use Zshwag\Bundle\WechatBundle\Security\Authentication\Token\MiniProgramUserToken;
use Zshwag\Bundle\WechatBundle\Security\Authentication\Exception\HttpTransportException;

/**
 * WeChat Mini-Program user login.
 * 
 * @author Hailong Zhao <hailongzh@hotmail.com>
 * 
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
 */
class MiniProgramProvider implements AuthenticationProviderInterface
{
    protected $filters = array(
        "openid"      => array("flags" => FILTER_REQUIRE_SCALAR),
        "session_key" => array("flags" => FILTER_REQUIRE_SCALAR),
        "unionid"     => array("flags" => FILTER_REQUIRE_SCALAR),
        "errcode"     => array("flags" => FILTER_REQUIRE_SCALAR),
        "errmsg"      => array("flags" => FILTER_REQUIRE_SCALAR),
    );

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var ServiceContainer
     */
    protected $app;

    public function __construct(UserProviderInterface $userProvider, ServiceContainer $app)
    {
        $this->userProvider = $userProvider;
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof MiniProgramUserToken;
    }

    public function authenticate(TokenInterface $token)
    {
        $response = new CodeToSessionResponse(
            $token->getCode(),
            $this->app->auth->session($token->getCode())
        );

        if ($response->getErrorCode()) {
            throw new AuthenticationException($response->getErrorCode(), $response->getErrorMessage());
        }

        try {
            $user = $this->userProvider->loadUserByCodeToSessionResponse($response);
        } catch (OAuthAwareExceptionInterface $e) {
            $e->setToken($token);
            $e->setResourceOwnerName($token->getResourceOwnerName());

            throw $e;
        }

        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException('loadUserByCodeToSessionResponse() must return a UserInterface.');
        }

        // $this->userChecker->checkPreAuth($user);
        // $this->userChecker->checkPostAuth($user);

        $token = new MiniProgramUserToken($token->getCode(), $user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);

        return $token;
    }
}
