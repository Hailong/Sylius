<?php

namespace Zshwag\Bundle\WechatBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Buzz\Client\ClientInterface;
use Buzz\Exception\ClientException;
use Buzz\Message\RequestInterface;
use Buzz\Message\Request;
use Buzz\Message\Response;
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
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $secret;

    public function __construct(UserProviderInterface $userProvider, ClientInterface $httpClient, string $appId, string $secret)
    {
        $this->userProvider = $userProvider;
        $this->httpClient = $httpClient;
        $this->appId = $appId;
        $this->secret = $secret;
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
        $response = $this->httpRequest($this->normalizeUrl('https://api.weixin.qq.com/sns/jscode2session', array(
            'appid' => $this->appId,
            'secret' => $this->secret,
            'js_code' => $token->getCode(),
            'grant_type' => 'authorization_code',
        )));

        $response = new CodeToSessionResponse($token->getCode(), $response);

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

    /**
     * @param string $url
     * @param array  $parameters
     *
     * @return string
     */
    protected function normalizeUrl($url, array $parameters = array())
    {
        $normalizedUrl = $url;
        if (!empty($parameters)) {
            $normalizedUrl .= (false !== strpos($url, '?') ? '&' : '?').http_build_query($parameters, '', '&');
        }

        return $normalizedUrl;
    }

    /**
     * Performs an HTTP request.
     *
     * @param string       $url     The url to fetch
     * @param string|array $content The content of the request
     * @param array        $headers The headers of the request
     * @param string       $method  The HTTP method to use
     *
     * @return HttpResponse The response content
     */
    protected function httpRequest($url, $content = null, $headers = array(), $method = null)
    {
        if (null === $method) {
            $method = null === $content || '' === $content ? RequestInterface::METHOD_GET : RequestInterface::METHOD_POST;
        }

        $request = new Request($method, $url);
        $response = new Response();

        $contentLength = 0;
        if (is_string($content)) {
            $contentLength = strlen($content);
        } elseif (is_array($content)) {
            $contentLength = strlen(implode('', $content));
        }

        $headers = array_merge(
            array(
                'User-Agent: ZshwagWechatBundle (https://github.com/Hailong/Sylius)',
                'Content-Length: '.$contentLength,
            ),
            $headers
        );

        $request->setHeaders($headers);
        $request->setContent($content);

        try {
            $this->httpClient->send($request, $response);
        } catch (ClientException $e) {
            throw new HttpTransportException('Error while sending HTTP request', $e->getCode(), $e);
        }

        return $response;
    }
}
