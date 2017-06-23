<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 8:53 PM
 */

namespace FannyPack\FcmHttp\Notifications;


use FannyPack\FcmHttp\Http\FcmHttp;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * @var FcmHttp
     */
    protected $fcmHttp;

    /** @var Dispatcher */
    protected $events;

    /**
     * FcmChannel constructor.
     * @param FcmHttp $fcmHttp
     * @param Dispatcher $events
     */
    public function __construct(FcmHttp $fcmHttp, Dispatcher $events)
    {
        $this->fcmHttp = $fcmHttp;
        $this->events = $events;
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
        
        $this->sendNotification($packet, $notifiable, $notification);
    }
    
    protected function sendNotification(FcmPacket $packet, $notifiable, $notification)
    {
        $response = $this->fcmHttp->sendMessage($packet);
        $body = $response->getBody();
        $contents = json_decode($body->getContents());
        
        if (! $contents['failure'] == 0) {

        }

        if (! $contents['canonical_ids'] == 0) {

        }
    }

}