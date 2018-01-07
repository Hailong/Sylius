<?php

namespace Zshwag\Bundle\WechatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zshwag\Bundle\WechatBundle\DependencyInjection\Security\Factory\MiniProgramFactory;

class ZshwagWechatBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // WeChat Mini-Program user login.
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new MiniProgramFactory());
    }
}
