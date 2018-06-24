<?php

namespace OnixSolutions\SmsTelera;

use Illuminate\Support\ServiceProvider;

class SmsTeleraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SmsTeleraApi::class, function ($app) {
            return new SmsTeleraApi($app['config']['services.smsctelera']);
        });
    }
}
