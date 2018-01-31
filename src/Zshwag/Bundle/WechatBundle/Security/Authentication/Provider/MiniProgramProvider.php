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

        $userinfo = $this->decryptUserInfo($response->getSessionKey(), $token->getEncryptedData(), $token->getIv());
        $user->setMiniProgramUserInfo($userinfo);

        $token = new MiniProgramUserToken($token->getCode(), '', '', $user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);

        return $token;
    }

    private function decryptUserInfo($sessionKey, $encryptData, $iv)
    {
        // This piece of code is cloned from the qlcoud server demo

        // 1. 获取 session key
        // $sessionKey = self::getSessionKey($code);

        // 2. 生成 3rd key (skey)
        $skey = sha1($sessionKey . mt_rand());

        /**
         * 3. 解密数据
         * 由于官方的解密方法不兼容 PHP 7.1+ 的版本
         * 这里弃用微信官方的解密方法
         * 采用推荐的 openssl_decrypt 方法（支持 >= 5.3.0 的 PHP）
         * @see http://php.net/manual/zh/function.openssl-decrypt.php
         */
        $decryptData = \openssl_decrypt(
            base64_decode($encryptData),
            'AES-128-CBC',
            base64_decode($sessionKey),
            OPENSSL_RAW_DATA,
            base64_decode($iv)
        );
        $userinfo = json_decode($decryptData);

        // 4. 储存到数据库中
        // User::storeUserInfo($userinfo, $skey, $sessionKey);

        // return compact('userinfo', 'skey');
        return $userinfo;
    }
}
