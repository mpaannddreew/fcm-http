<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2017-06-21
 * Time: 9:39 PM
 */

namespace FannyPack\Fcm\Http\Facades;


use FannyPack\Fcm\Http\HttpClient;
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
        return HttpClient::class;
    }
}