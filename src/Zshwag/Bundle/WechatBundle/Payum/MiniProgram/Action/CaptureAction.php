<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action;

use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Exception\RequestNotSupportedException;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Request\Api\UnifiedOrder;

class CaptureAction extends GatewayAwareAction
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        /** @var $request Capture */
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());
        if (isset($httpRequest->query['cancelled'])) {
            $details['CANCELLED'] = true;

            return;
        }

        if (false == $details['notify_url'] && $request->getToken()) {
            $details['notify_url'] = $request->getToken()->getTargetUrl();
        }

        $this->gateway->execute(new UnifiedOrder($details));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
