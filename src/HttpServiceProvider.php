<?php

namespace FannyPack\Fcm\Http;

use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
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
        $this->app->bind(HttpClient::class, function($app){
            return new HttpClient($app);
        });
    }

    public function provides()
    {
        return [HttpClient::class];
    }
}
