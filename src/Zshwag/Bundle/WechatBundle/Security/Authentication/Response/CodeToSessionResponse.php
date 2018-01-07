<?php

namespace Zshwag\Bundle\WechatBundle\Security\Authentication\Response;

use Buzz\Message\MessageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * WeChat Mini-Program user login.
 * 
 * @author Hailong Zhao <hailongzh@hotmail.com>
 * 
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
 */
class CodeToSessionResponse
{
    protected $filters = array(
        "openid"      => array("flags" => FILTER_REQUIRE_SCALAR),
        "session_key" => array("flags" => FILTER_REQUIRE_SCALAR),
        "unionid"     => array("flags" => FILTER_REQUIRE_SCALAR),
        "errcode"     => array("flags" => FILTER_REQUIRE_SCALAR),
        "errmsg"      => array("flags" => FILTER_REQUIRE_SCALAR),
    );

    /**
     * @var string
     */
    protected $jsCode;

    /**
     * @var MessageInterface
     */
    protected $response;

    /**
     * @var array
     */
    protected $content;

    /**
     * @param string $jsCode
     * @param MessageInterface $response
     */
    public function __construct($jsCode, MessageInterface $response)
    {
        $this->jsCode = $jsCode;
        $this->response = $response;

        $content = json_decode($this->response->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new AuthenticationException('Response is not a valid JSON code.');
        }

        $this->content = filter_var_array($content, $this->filters);

        if (!$this->content['openid'] || !$this->content['session_key']) {
            throw new AuthenticationException($response->getContent());
        }
    }

    public function getJsCode()
    {
        return $this->jsCode;
    }

    public function getErrorCode()
    {
        return $this->content['errcode'];
    }

    public function getErrorMessage()
    {
        return $this->content['errmsg'];
    }

    public function getOpenId()
    {
        return $this->content['openid'];
    }

    public function getSessionKey()
    {
        return $this->content['session_key'];
    }

    public function getUnionid()
    {
        return $this->content['unionid'];
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return sprintf('wx%s', $this->getOpenId());
    }

    /**
     * Get the email address.
     *
     * @return null|string
     */
    public function getEmail()
    {
        return sprintf('hxiyouz+%s@gmail.com', $this->getUsername());
    }
}
