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

        $model->replace(
            $this->api->unifiedOrder((array) $model)
        );

        throw new HttpResponse(json_encode(array_merge(
            ['afterUrl' => $model['afterUrl']],
            $this->api->bridgeConfig($model['prepay_id'])
        )));
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
