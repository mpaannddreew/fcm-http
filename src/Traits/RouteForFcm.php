<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 8:46 PM
 */

namespace FannyPack\FcmHttp\Traits;


trait RouteForFcm
{
    /**
     * @return mixed
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_registration_id;
    }
}