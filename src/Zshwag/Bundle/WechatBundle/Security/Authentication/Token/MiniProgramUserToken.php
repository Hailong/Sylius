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

    public function __construct(string $code, array $roles = array())
    {
        parent::__construct($roles);

        $this->code = $code;

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
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return null;
    }
}
