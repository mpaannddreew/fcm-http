<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 8:53 PM
 */

namespace FannyPack\FcmHttp\Notifications;


use FannyPack\FcmHttp\Http\FcmHttp;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * @var FcmHttp
     */
    protected $fcmHttp;

    /**
     * FcmChannel constructor.
     * @param FcmHttp $fcmHttp
     */
    public function __construct(FcmHttp $fcmHttp)
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

        $message = $notification->toFcm($notifiable);
        
        if (!($message && is_a($message, FcmMessage::class))) {
            return;
        }

        $packet = (new FcmPacket())
            ->message($message)
            ->toMany($registration_ids);

        $this->fcmHttp->sendMessage($packet);
    }
}