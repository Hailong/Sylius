<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpResponse;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Request\Api\UnifiedOrder;

class UnifiedOrderAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request UnifiedOrder */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $order = $this->api->unifiedOrder([
            'body' => $model['body'],
            'out_trade_no' => $model['out_trade_no'],
            'total_fee' => $model['total_fee'],
            'notify_url' => $model['notify_url'],
            'trade_type' => $model['trade_type'],
            'openid' => $model['openid'],
        ]);
        $model->replace($order);

        if (isset($order['prepay_id'])) {
            $resp = array_merge(
                ['afterUrl' => $model['afterUrl']],
                $this->api->bridgeConfig($model['prepay_id'])
            );
        } else {
            $resp = $order;
        }

        throw new HttpResponse(json_encode($resp));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof UnifiedOrder &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
