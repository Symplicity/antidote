<?php

use Laravel\Lumen\Application;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->group(['prefix' => 'api'],
    function (Application $app) {
        $app->get('users',
            [
                'uses' => 'App\Http\Controllers\UserController@index',
                'as' => 'user.index',
            ]);

        $app->get('drugs/{ndc}',
            [
                'uses' => 'App\Http\Controllers\DrugController@show',
                'as' => 'drug.show',
            ]);
    });
