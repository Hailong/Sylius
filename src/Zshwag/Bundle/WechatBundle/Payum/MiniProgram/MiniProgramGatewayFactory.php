<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram;

use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\AuthorizeAction;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\CancelAction;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\ConvertPaymentAction;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\CaptureAction;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\NotifyAction;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\RefundAction;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\StatusAction;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\Api\UnifiedOrderAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class MiniProgramGatewayFactory extends GatewayFactory
{
    protected $app;

    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'miniprog',
            'payum.factory_title' => 'miniprog',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.api.unified_order' => new UnifiedOrderAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $api = new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
                $api->setApp($this->app);

                return $api;
            };
        }
    }
}
