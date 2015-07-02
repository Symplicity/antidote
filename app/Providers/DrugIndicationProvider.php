<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DrugIndicationProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('DrugIndication', function () {
            return new \App\DrugIndication();
        });
    }
}
