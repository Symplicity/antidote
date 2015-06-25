<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DrugProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Drug', function () {
            return new \App\Drug();
        });
    }
}
