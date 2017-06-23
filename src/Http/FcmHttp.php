<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 4:47 PM
 */

namespace FannyPack\FcmHttp\Http;


use FannyPack\FcmHttp\Notifications\FcmPacket;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;

class FcmHttp
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
     * array of request headers
     * 
     * @var array
     */
    protected $headers;

    /**
     * FCM http connection server endpoint
     * 
     * @var string
     */
    const ENDPOINT_URL = "https://fcm.googleapis.com/fcm/send";

    /**
     * FcmHttp constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->apiKey = $this->getApiKey();
        $this->headers = $this->getRequestOptions();
        $this->httpClient = $this->getClient();
    }

    /**
     * create http client
     * 
     * @return Client
     */
    protected function getClient()
    {
        return new Client([
            'headers' => $this->headers,
            'verify' => false
        ]);
    }

    /**
     * get FCM server key
     * 
     * @return string
     */
    protected function getApiKey()
    {
        return $this->app['config']['fcmhttp.apiKey'];
    }

    /**
     * get request headers
     * 
     * @return array
     */
    protected function getRequestOptions()
    {
        return [
            'Authorization' => 'key=' . $this->apiKey,
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * send message to FCM
     * 
     * @param FcmPacket $packet
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendMessage(FcmPacket $packet)
    {
        $response = $this->httpClient->post(self::ENDPOINT_URL, ['form_params' => $packet->toArray()]);

        return $response;
    }
}