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
        $tokens = (array) $notifiable->routeNotificationFor('fcm');
        if (! $tokens) {
            return;
        }

        $message = $notification->toFcm($notifiable);
        
        if ($message && $message instanceof FcmMessage::class) {
            $packet = (new FcmPacket())
                ->message($message)
                ->toMany($tokens);
            $this->sendNotification($packet);
        }elseif($message && $message instanceof FcmPacket::class)
        {
            $this->sendNotification($message);
        }else
        {
            return;
        }
        
        // Send notification to the $notifiable instance...
    }
    
    protected function sendNotification(FcmPacket $packet)
    {
        $response = $this->fcmHttp->sendMessage($packet);
    }
}