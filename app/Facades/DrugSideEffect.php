<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DrugSideEffect extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'DrugSideEffect';
    }
}
