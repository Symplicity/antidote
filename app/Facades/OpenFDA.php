<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OpenFDA extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OpenFDA';
    }
}
