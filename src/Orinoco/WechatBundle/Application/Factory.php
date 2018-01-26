<?php

namespace Orinoco\WechatBundle\Application;

use EasyWeChat\Factory as EasyWeChatFactory;

class Factory
{
    /**
     * @param string $name
     * @param array  $config
     *
     * @return EasyWeChat\Kernel\ServiceContainer
     */
    public function createApplication($name, array $config)
    {
        return EasyWeChatFactory::make($name, $config);
    }
}
