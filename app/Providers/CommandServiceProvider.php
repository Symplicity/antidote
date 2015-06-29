<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('import.drugs', function () {
            return new Commands\ImportDrugs();
        });

        $this->app->singleton('make.token', function () {
            return new Commands\MakeToken();
        });

        $this->commands(
            'import.drugs',
            'make.token'
        );
    }
}
