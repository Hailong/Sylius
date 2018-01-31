<?php

namespace Zshwag\Bundle\WechatBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * WeChat Mini-Program user login.
 * 
 * @author Hailong Zhao <hailongzh@hotmail.com>
 * 
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
 */
class MiniProgramUserToken extends AbstractToken
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $encryptedData;

    /**
     * @var string
     */
    private $iv;

    public function __construct(string $code, string $encryptedData, string $iv, array $roles = array())
    {
        parent::__construct($roles);

        $this->code = $code;
        $this->encryptedData = $encryptedData;
        $this->iv = $iv;

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getEncryptedData()
    {
        return $this->encryptedData;
    }

    /**
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return null;
    }
}
