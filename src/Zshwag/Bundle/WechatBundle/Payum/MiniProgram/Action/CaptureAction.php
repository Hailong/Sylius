<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action;

use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpResponse;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Request\Api\UnifiedOrder;
use Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Request\Api\HandlePaidNotify;

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

        if ($details['transaction_id']) {
            throw new HttpResponse('paid');
        }

        $this->gateway->execute($httpRequest = new GetHttpRequest());
        if (isset($httpRequest->query['cancelled'])) {
            $details['CANCELLED'] = true;

            return;
        }

        if ($httpRequest->method == 'POST') {
            // notify request from WeChat
            $this->gateway->execute(new HandlePaidNotify($details));
        }

        if ($details['prepay_id'] == null) {
            if (false == $details['notify_url'] && $request->getToken()) {
                $details['notify_url'] = $request->getToken()->getTargetUrl();
            }

            $details['afterUrl'] = $request->getToken()->getAfterUrl();

            $this->gateway->execute(new UnifiedOrder($details));
        }
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
