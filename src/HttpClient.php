<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 4:47 PM
 */

namespace FannyPack\Fcm\Http;


use FannyPack\Utils\Fcm\Events\RegistrationErrorEvent;
use FannyPack\Utils\Fcm\Events\RegistrationExpiryEvent;
use FannyPack\Utils\Fcm\Events\UnavailableErrorEvent;
use FannyPack\Utils\Fcm\Packet;
use FannyPack\Utils\Fcm\Response;
use GuzzleHttp\Client;
use Illuminate\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Psr\Http\Message\ResponseInterface;

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
     * @var Dispatcher
     */
    protected $events;

    /**
     * Http constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->events = $this->app['events'];
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
     */
    public function sendMessage(Packet $packet)
    {
        if ($packet->getPipeline() != Packet::HTTP_PIPELINE)
            throw new \InvalidArgumentException('Packet should be of http pipeline');

        $response = $this->httpClient->post(self::ENDPOINT_URL, ['json' => $packet->toArray()]);
        $this->processResponse($response, $packet);
    }

    /**
     * @param ResponseInterface $response
     * @param Packet $packet
     */
    protected function processResponse(ResponseInterface $response, Packet $packet)
    {
        $registrationIds = $packet->getRegistrationIds();
        $response = new Response($response);
        if ($response->getFailure() != 0 || $response->getCanonicalIds() != 0)
        {
            $results = $response->getResults();
            foreach ($results as $key => $result)
            {
                $old_registration_id = $registrationIds[$key];
                if (isset($result['message_id'])) {
                    if (isset($result['registration_id'])) {
                        // fcm registration id expired
                        $new_registration_id = $result['registration_id'];
                        $this->events->fire(new RegistrationExpiryEvent($old_registration_id, $new_registration_id));
                    };
                }

                if (isset($result['error'])) {
                    switch ($result['error']){
                        case 'Unavailable':
                            $this->events->fire(new UnavailableErrorEvent($old_registration_id, $packet));
                            break;
                        case 'InvalidRegistration':
                            // app uninstalled by user
                            $this->events->fire(new RegistrationErrorEvent($old_registration_id));
                            break;
                        case 'NotRegistered':
                            // app uninstalled by user
                            $this->events->fire(new RegistrationErrorEvent($old_registration_id));
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }
}