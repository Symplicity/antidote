<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OpenFDA;

class OpenFDAServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->app->singleton('OpenFDA', function ($app) {
            return new OpenFDA($app['Guzzle'], $app['config']['openfda']);
        });
    }
}
