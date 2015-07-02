<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DrugReview extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'DrugReview';
    }
}
