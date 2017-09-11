<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 4:47 PM
 */

namespace FannyPack\Fcm\Http;


use FannyPack\Utils\Fcm\Packet;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;

class HttpClient
{
    /**
     * http client
     * 
     * @var Client
     */
    protected $httpClient;

    /**
     * FCM server key
     * 
     * @var string
     */
    protected $apiKey;

    /**
     * @var Application
     */
    protected $app;

    /**
     * array of request options
     * 
     * @var array
     */
    protected $options;

    /**
     * FCM http connection server endpoint
     * 
     * @var string
     */
    const ENDPOINT_URL = "https://fcm.googleapis.com/fcm/send";

    /**
     * Http constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->setApiKey();
        $this->setRequestOptions();
        $this->setClient();
    }

    /**
     * create http client
     * 
     * @return void
     */
    protected function setClient()
    {
        $this->httpClient =  new Client($this->options);
    }

    /**
     * get FCM server key
     * 
     * @return void
     */
    protected function setApiKey()
    {
        $key = $this->app['config']['fcmhttp.apiKey'];
        if (!$key)
            throw new \InvalidArgumentException("FCM Server key not specified");

        $this->apiKey =  $key;
    }

    /**
     * get request options
     * 
     * @return void
     */
    protected function setRequestOptions()
    {
        $this->options =  [
            'headers' => [
                'Authorization' => 'key=' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'verify' => false
        ];
    }

    /**
     * send message to FCM
     * 
     * @param Packet $packet
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendMessage(Packet $packet)
    {
        if ($packet->getPipeline() != Packet::HTTP_PIPELINE)
            throw new \InvalidArgumentException('Packet should be of http pipeline');

        $response = $this->httpClient->post(self::ENDPOINT_URL, ['json' => $packet->toArray()]);

        return $response;
    }
}