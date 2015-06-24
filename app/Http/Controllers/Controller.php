<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    protected function getLimit(Request $request)
    {
        $limit = 15;

        if ($request['limit']) {
            $limit = $request['limit'];
        }

        return $limit;
    }
}
