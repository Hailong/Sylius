<?php

namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram;

use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Payum\Core\GatewayFactoryInterface;
use EasyWeChat\Kernel\ServiceContainer;

class MiniProgramGatewayFactoryBuilder extends GatewayFactoryBuilder
{
    /**
     * @var string
     */
    private $gatewayFactoryClass;

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @param string $gatewayFactoryClass
     * @param ServiceContainer $app
     */
    public function __construct($gatewayFactoryClass, $app)
    {
        $this->gatewayFactoryClass = $gatewayFactoryClass;
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $defaultConfig, GatewayFactoryInterface $coreGatewayFactory)
    {
        $gatewayFactoryClass = $this->gatewayFactoryClass;

        $factory = new $gatewayFactoryClass($defaultConfig, $coreGatewayFactory);
        $factory->setApp($this->app);

        return $factory;
    }
}
