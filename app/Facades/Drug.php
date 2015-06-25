<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Drug extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Drug';
    }
}
