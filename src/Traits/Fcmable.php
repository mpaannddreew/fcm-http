<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 8:46 PM
 */

namespace FannyPack\FcmHttp\Traits;


trait Fcmable
{
    /**
     * @return mixed
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_target;
    }
}