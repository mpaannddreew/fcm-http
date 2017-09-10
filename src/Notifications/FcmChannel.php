<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 8:53 PM
 */

namespace FannyPack\Fcm\Http\Notifications;


use FannyPack\Fcm\Http\Http;
use FannyPack\Utils\Fcm\Messages\Payload;
use FannyPack\Utils\Fcm\Packet;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * @var Http
     */
    protected $fcmHttp;

    /**
     * FcmChannel constructor.
     * @param Http $fcmHttp
     */
    public function __construct(Http $fcmHttp)
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
        
        if (!($payload && is_a($payload, Payload::class))) {
            return;
        }

        $packet = (new Packet())
            ->setPayload($payload)
            ->setRegistrationIds($registration_ids);

        $this->fcmHttp->sendMessage($packet);
    }
}