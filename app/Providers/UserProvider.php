<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UserProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('User', function () {
            return new \App\User();
        });
    }
}
