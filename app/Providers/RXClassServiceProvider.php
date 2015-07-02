<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RXClass;

class RXClassServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->app->singleton('RXClass', function ($app) {
            return new RXClass($app['GuzzleHttp\Client']);
        });
    }
}
