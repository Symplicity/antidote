<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class OpenFDAServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->app->singleton('App\Services\OpenFDA', function () {
            return new OpenFDA(Config::get('openfda.api_base_uri'));
        });
    }
}
