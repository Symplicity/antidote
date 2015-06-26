<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\ImportDrugs;

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
            return new ImportDrugs();
        });

        $this->commands(
            'import.drugs'
        );
    }
}
