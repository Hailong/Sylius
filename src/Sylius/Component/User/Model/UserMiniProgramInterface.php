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

use Sylius\Component\Resource\Model\ResourceInterface;

interface UserMiniProgramInterface extends UserAwareInterface, ResourceInterface
{
    /**
     * @return string|null
     */
    public function getOpenId(): ?string;

    /**
     * @param string|null $openId
     */
    public function setOpenId(?string $openId): void;

    /**
     * @return string|null
     */
    public function getUnionId(): ?string;

    /**
     * @param string|null $unionId
     */
    public function setUnionId(?string $unionId): void;

    /**
     * @return string|null
     */
    public function getJsCode(): ?string;

    /**
     * @param string|null $accessToken
     */
    public function setJsCode(?string $jsCode): void;

    /**
     * @return string|null
     */
    public function getSessionKey(): ?string;

    /**
     * @param string|null $sessionKey
     */
    public function setSessionKey(?string $sessionKey): void;

    /**
     * @return int
     */
    public function getLastLogin(): int;

    /**
     * @param int $timestamp
     */
    public function setLastLogin(int $timestamp): void;
}
