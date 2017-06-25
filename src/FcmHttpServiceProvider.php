<?php

namespace FannyPack\FcmHttp;

use FannyPack\FcmHttp\Http\FcmHttp;
use Illuminate\Support\ServiceProvider;

class FcmHttpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/fcmhttp.php' => config_path('fcmhttp.php'),
            ], 'fcm-http-config');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FcmHttp::class, function($app){
            return new FcmHttp($app);
        });
    }

    public function provides()
    {
        return [FcmHttp::class];
    }
}
