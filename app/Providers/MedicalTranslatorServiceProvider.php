<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MedicalTranslator;

class MedicalTranslatorServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->app->singleton('MedicalTranslator', function ($app) {
            return new MedicalTranslator();
        });
    }
}
