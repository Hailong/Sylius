<?php

namespace Orinoco\Payment\GatewayBundle\Wechat;

class WechatGatewayException extends \Exception {
    protected $error;

    public function __construct(array $error)
    {
        parent::__construct('WechatGatewayException');

        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }
}
