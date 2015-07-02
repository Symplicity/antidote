<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DrugSideEffectProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('DrugSideEffect', function () {
            return new \App\DrugSideEffect();
        });
    }
}
