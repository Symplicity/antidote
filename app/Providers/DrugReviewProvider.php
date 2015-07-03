<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DrugReviewProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('DrugReview', function () {
            return new \App\DrugReview();
        });
    }
}
