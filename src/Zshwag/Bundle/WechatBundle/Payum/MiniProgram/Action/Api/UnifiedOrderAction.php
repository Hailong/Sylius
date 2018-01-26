<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
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
