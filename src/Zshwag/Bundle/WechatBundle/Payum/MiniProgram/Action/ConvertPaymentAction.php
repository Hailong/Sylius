<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\PaymentInterface;

class ConvertPaymentAction implements ActionInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $miniProgramAccounts = $order->getUser()->getMiniProgramAccounts();

        $details = [
            'body'         => '专属海外购-订单支付',
            'out_trade_no' => $order->getNumber(),
            'total_fee'    => $order->getTotal(),
            'trade_type'   => 'JSAPI',
            'openid'       => $miniProgramAccounts[0]->getOpenId(),
        ];

        $request->setResult($details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() == 'array'
        ;
    }
}
