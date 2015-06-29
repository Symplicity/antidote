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
        $app->post('auth/login', 'App\Http\Controllers\AuthController@login');
        $app->post('auth/signup', 'App\Http\Controllers\AuthController@signup');
        $app->post('auth/forgot', 'App\Http\Controllers\AuthController@processForgotPassword');

        $app->get('auth/reset/{token}', 'App\Http\Controllers\AuthController@verifyResetPasswordToken');

        $app->post('auth/reset/{token}', 'App\Http\Controllers\AuthController@updatePasswordFromResetToken');

        $app->get('users/me', ['middleware' => 'auth', 'uses' => 'App\Http\Controllers\UserController@getUser']);
        $app->put('users/me', ['middleware' => 'auth', 'uses' => 'App\Http\Controllers\UserController@updateUser']);

        $app->get('drugs', 'App\Http\Controllers\DrugController@index');
        $app->get('drugs/{id}', 'App\Http\Controllers\DrugController@show');
        $app->get('drugs/{id}/reviews', 'App\Http\Controllers\DrugController@getReviews');
        $app->post('drugs/{id}/reviews', ['middleware' => 'auth', 'uses' => 'App\Http\Controllers\DrugController@addReview']);

        $app->get('drugs/{id}/alternatives', 'App\Http\Controllers\DrugController@getAlternatives');

        $app->post('drug-reviews/{id}/vote', ['middleware' => 'auth', 'uses' => 'App\Http\Controllers\DrugReviewsController@vote']);
    });
