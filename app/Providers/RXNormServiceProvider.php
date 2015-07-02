<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RXNorm;

class RXNormServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->app->singleton('RXNorm', function ($app) {
            return new RXNorm($app['GuzzleHttp\Client']);
        });
    }
}
