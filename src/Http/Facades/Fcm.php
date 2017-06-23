<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 9:39 PM
 */

namespace FannyPack\FcmHttp\Http\Facades;


use FannyPack\FcmHttp\Http\FcmHttp;
use Illuminate\Support\Facades\Facade;

class Fcm extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return FcmHttp::class;
    }
}