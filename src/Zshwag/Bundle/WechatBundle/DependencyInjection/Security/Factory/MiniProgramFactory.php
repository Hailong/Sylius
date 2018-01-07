<?php

namespace Zshwag\Bundle\WechatBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

/**
 * WeChat Mini-Program user login.
 * 
 * @author Hailong Zhao <hailongzh@hotmail.com>
 * 
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
 */
class MiniProgramFactory extends AbstractFactory
{
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'security.authentication.provider.mini_program.'.$id;

        $container
            ->setDefinition($providerId, new DefinitionDecorator('security.authentication.provider.mini_program'))
            ->addArgument(new Reference($userProviderId))
            ->addArgument(new Reference('zshwag.http_client'))
            ->addArgument($config['app_id'])
            ->addArgument($config['secret'])
        ;

        return $providerId;
    }

    protected function getListenerId()
    {
        return 'security.authentication.listener.mini_program';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $builder = $node->children();
        $builder
            ->scalarNode('app_id')->cannotBeEmpty()->isRequired()->end()
            ->scalarNode('secret')->cannotBeEmpty()->isRequired()->end()
        ;
    }

    public function getKey()
    {
        return 'mini-program';
    }

    public function getPosition()
    {
        return 'pre_auth';
    }
}
