<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Bridge\Symfony\Reply\HttpResponse as SymfonyHttpResponse;
use Payum\Core\Reply\HttpResponse;
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

        try {
            $resp = $this->api->handlePaidNotify(function ($message, $fail) use ($model) {
                $model->replace($message);

                return true;
            });
        } catch (\Exception $e) {
            throw new HttpResponse($e->getMessage(), $e->getCode());
        }

        throw new SymfonyHttpResponse($resp);
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
