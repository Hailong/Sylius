<?php
namespace Zshwag\Bundle\WechatBundle\Payum\MiniProgram;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use EasyWeChat\Kernel\ServiceContainer;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param ServiceContainer $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    public function unifiedOrder(array $fields)
    {
        return $this->app->order->unify($fields);
    }

    /**
     * @param string $prepayId
     * 
     * @return array
     */
    public function bridgeConfig($prepayId)
    {
        return $this->app->jssdk->bridgeConfig($prepayId);
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    protected function doRequest($method, array $fields)
    {
        $headers = [];

        $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint(), $headers, http_build_query($fields));

        $response = $this->client->send($request);

        if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw HttpException::factory($request, $response);
        }

        return $response;
    }

    /**
     * @return string
     */
    protected function getApiEndpoint()
    {
        return $this->options['sandbox'] ? 'http://sandbox.example.com' : 'http://example.com';
    }
}
