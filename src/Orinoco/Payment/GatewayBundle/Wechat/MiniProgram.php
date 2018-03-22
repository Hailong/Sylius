<?php

namespace Orinoco\Payment\GatewayBundle\Wechat;

use EasyWeChat\Factory;

class MiniProgram
{
    protected $app;

    public function __construct($app_id, $secret, $logFile)
    {
        $config = [
            'app_id' => $app_id,
            'secret' => $secret,

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => $logFile,
            ],
        ];

        $this->app = Factory::miniProgram($config);
    }

    public function session(string $code)
    {
        return $this->app->auth->session($code);
    }
}
