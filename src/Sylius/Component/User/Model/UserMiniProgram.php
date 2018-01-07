<?php

/**
 * WeChat Mini-Program user login.
 * 
 * @author Hailong Zhao <hailongzh@hotmail.com>
 * 
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
 */

declare(strict_types=1);

namespace Sylius\Component\User\Model;

class UserMiniProgram implements UserMiniProgramInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $openId;

    /**
     * @var string|null
     */
    protected $unionId;

    /**
     * @var string|null
     */
    protected $jsCode;

    /**
     * @var string|null
     */
    protected $sessionKey;

    /**
     * @var int
     */
    protected $lastLogin;

    /**
     * @var UserInterface|null
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    /**
     * {@inheritdoc}
     */
    public function setOpenId(?string $openId): void
    {
        $this->openId = $openId;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnionId(?string $unionId): void
    {
        $this->unionId = $unionId;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsCode(): ?string
    {
        return $this->jsCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setJsCode(?string $jsCode): void
    {
        $this->jsCode = $jsCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionKey(): ?string
    {
        return $this->sessionKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setSessionKey(?string $sessionKey): void
    {
        $this->sessionKey = $sessionKey;
    }

    /**
     * @return int
     */
    public function getLastLogin(): int
    {
        return $this->lastLogin;
    }

    /**
     * @param int $timestamp
     */
    public function setLastLogin(int $timestamp): void
    {
        $this->lastLogin = $timestamp;
    }
}
