<?php

namespace Orinoco\Payment\GatewayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyWeChat\Kernel\Exceptions\HttpException as EasyWeChatHttpException;
use Orinoco\Payment\GatewayBundle\Wechat\WechatGatewayException;

class WechatController extends Controller
{
    /**
     * Request params:
     *
     *  code: 
     *  body: '腾讯充值中心-QQ会员充值'
     *  out_trade_no: '20150806125346'
     *  total_fee: 88
     */
    public function unifiedOrderAction(Request $request, $app)
    {
        $miniProgram = $this->get('orinoco_payment_gateway.wechat.mini_program.' . $app);
        $payment = $this->get('orinoco_payment_gateway.wechat.payment.' . $app);

        try {
            $session = $miniProgram->session($request->get('code'));

            if (!isset($session['openid']) || !isset($session['session_key'])) {
                return $this->json($session);
            }

            $result = $payment->unify([
                'body' => $request->get('body'),
                'out_trade_no' => $request->get('out_trade_no'),
                'total_fee' => $request->get('total_fee'),
                'notify_url' => $this->generateUrl('orinoco_payment_gateway_wechat_notify', $parameters = ['app' => $app]), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                'trade_type' => 'JSAPI',
                'openid' => $session['openid'],
            ]);

            return $this->json($result);

        } catch (EasyWeChatHttpException $e) {
            return new Response(
                $e->response->getBody(),
                $e->response->getStatusCode(),
                $e->response->getHeaders()
            );
        } catch (WechatGatewayException $e) {
            return $this->json($e->getError());
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }

        return $this->render('OrinocoPaymentGatewayBundle:Default:index.html.twig');
    }

    public function notifyAction(Request $request, $app)
    {
    }
}
