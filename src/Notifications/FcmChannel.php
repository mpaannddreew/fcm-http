<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 8:53 PM
 */

namespace FannyPack\Fcm\Http\Notifications;


use FannyPack\Fcm\Http\HttpClient;
use FannyPack\Utils\Fcm\Messages\Payload;
use FannyPack\Utils\Fcm\Packet;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * @var HttpClient
     */
    protected $fcmHttp;

    /**
     * FcmChannel constructor.
     * @param HttpClient $fcmHttp
     */
    public function __construct(HttpClient $fcmHttp)
    {
        $this->fcmHttp = $fcmHttp;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $registration_ids = (array) $notifiable->routeNotificationFor('fcm');
        if (! $registration_ids) {
            return;
        }

        $payload = $notification->toFcm($notifiable);
        
        if (!($payload && (get_class($payload) == Payload::class))) {
            return;
        }

        $packet = (new Packet())
            ->setPayload($payload)
            ->setRegistrationIds($registration_ids);

        $this->fcmHttp->sendMessage($packet);
    }
}