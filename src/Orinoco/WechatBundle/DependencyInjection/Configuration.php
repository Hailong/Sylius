<?php

namespace Orinoco\WechatBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('orinoco_wechat');

        $rootNode
            ->children()
                ->booleanNode('sandbox')
                    ->defaultTrue()
                ->end()
                ->arrayNode('log')
                    ->addDefaultsIfNotSet()
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('level')
                            ->defaultValue('debug')
                        ->end()
                        ->scalarNode('file')
                            ->defaultValue('%kernel.root_dir%/../var/logs/wechat.log')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('applications')
                    ->append($this->addOfficialAccountSection())
                    ->append($this->addPaymentSection())
                    ->append($this->addMiniProgramSection())
                    ->append($this->addOpenPlatformSection())
                    ->append($this->addWorkSection())
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addOfficialAccountSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('official_account');

        $node
            ->canBeEnabled()
            ->children()
                ->scalarNode('app_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('response_type')
                    ->info('指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名')
                    ->defaultValue('array')
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addPaymentSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('payment');

        $node
            ->canBeEnabled()
            ->children()
                ->scalarNode('app_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('mch_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('key')
                    ->info('API 密钥')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('cert_path')
                    ->info('如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)')
                ->end()
                ->scalarNode('key_path')
                    ->info('如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)')
                ->end()
                ->scalarNode('notify_url')
                    ->info('默认的订单回调地址，你也可以在下单时单独设置来想覆盖它')
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addMiniProgramSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('mini_program');

        $node
            ->addDefaultsIfNotSet()
            ->canBeEnabled()
            ->children()
            ->end()
        ;

        return $node;
    }

    private function addOpenPlatformSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('open_platform');

        $node
            ->canBeEnabled()
            ->children()
                ->scalarNode('app_id')
                    ->info('开放平台第三方平台 APPID')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('secret')
                    ->info('开放平台第三方平台 Secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('token')
                    ->info('开放平台第三方平台 Token')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('aes_key')
                    ->info('开放平台第三方平台 AES Key')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addWorkSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('work');

        $node
            ->canBeEnabled()
            ->children()
                ->scalarNode('corp_id')
                    ->info('开放平台第三方平台 APPID')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('agent_id')
                    ->info('如果有 agend_id 则填写')
                ->end()
                ->scalarNode('secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('response_type')
                    ->info('指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名')
                    ->defaultValue('array')
                ->end()
            ->end()
        ;

        return $node;
    }
}
