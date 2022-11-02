<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('register', 'AuthController@register');
});

Route::group([
    'middleware' => 'jwt.auth'
], function () {
    Route::prefix('slider')->group(function () {
        Route::post('list', 'SliderController@list');
        Route::post('add', 'SliderController@add');
        Route::get('get/{id}', 'SliderController@get');
    });
});
