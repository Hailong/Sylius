<?php

namespace Orinoco\WechatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Orinoco\WechatBundle\Application\Application;
use Orinoco\WechatBundle\Application\Factory;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class OrinocoWechatExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (!isset($config['applications'])) {
            return;
        }

        $applications = $config['applications'];
        unset($config['applications']);

        // setup services for all configured applications
        foreach ($applications as $name => $options) {
            $this->createApplicationService($container, $name, array_merge($config, $options));
        }
    }

    /**
     * Creates an application service.
     *
     * @param ContainerBuilder $container The container builder
     * @param string           $name      The name of the application
     * @param array            $options   Options of the application
     */
    public function createApplicationService(ContainerBuilder $container, $name, array $options)
    {
        $definition = new Definition(ServiceContainer::class);
        $definition->setFactory(array(new Reference('orinoco_wechat.application.factory'), 'createApplication'));
        $definition->setArguments(array($name, $options));
        $container->setDefinition('orinoco_wechat.application.'.$name, $definition);
    }
}
