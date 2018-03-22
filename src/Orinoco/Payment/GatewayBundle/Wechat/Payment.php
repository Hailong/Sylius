<?php

namespace Orinoco\Payment\GatewayBundle\Wechat;

use EasyWeChat\Factory;

class Payment
{
    protected $app;

    public function __construct($app_id, $mch_id, $key, $cert_path, $key_path)
    {
        $config = [
            // 必要配置
            'app_id'             => $app_id,
            'mch_id'             => $mch_id,
            'key'                => $key,   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            // 'cert_path'          => $cert_path, // XXX: 绝对路径！！！！
            // 'key_path'           => $key_path,      // XXX: 绝对路径！！！！

            'notify_url'         => '',     // 你也可以在下单时单独设置来想覆盖它
        ];

        $this->app = Factory::payment($config);
    }

    public function unify($params)
    {
        return $this->app->order->unify($params);
    }
}
