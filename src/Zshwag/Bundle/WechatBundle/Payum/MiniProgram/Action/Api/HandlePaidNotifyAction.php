<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Bridge\Symfony\Reply\HttpResponse;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Request\Api\HandlePaidNotify;

class HandlePaidNotifyAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request UnifiedOrder */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $resp = $this->api->handlePaidNotify(function ($message, $fail) {
            $model->replace($message);

            return true;
        });

        throw new HttpResponse($resp);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof HandlePaidNotify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
