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
    // Slider
    Route::prefix('slider')->group(function () {
        Route::post('list', 'SliderController@list');
        Route::post('add', 'SliderController@add');
        Route::get('get/{id}', 'SliderController@get');
        Route::put('edit', 'SliderController@edit');
        Route::delete('delete/{id}', 'SliderController@delete');
    });

    // Testimonial
    Route::prefix('testimonial')->group(function () {
        Route::post('list', 'TestimonialController@list');
        Route::post('add', 'TestimonialController@add');
        Route::get('get/{id}', 'TestimonialController@get');
        Route::put('edit', 'TestimonialController@edit');
        Route::delete('delete/{id}', 'TestimonialController@delete');
    });

    // Beauty Image
    Route::prefix('beauty-image')->group(function () {
        Route::post('list', 'BeautyImageController@list');
        Route::post('add', 'BeautyImageController@add');
        Route::get('get/{id}', 'BeautyImageController@get');
        Route::put('edit', 'BeautyImageController@edit');
        Route::delete('delete/{id}', 'BeautyImageController@delete');
    });

    // Faq
    Route::prefix('faq')->group(function () {
        Route::post('list', 'FaqController@list');
        Route::post('add', 'FaqController@add');
        Route::get('get/{id}', 'FaqController@get');
        Route::put('edit', 'FaqController@edit');
        Route::delete('delete/{id}', 'FaqController@delete');
    });

    // Contact Setting
    Route::prefix('contact-setting')->group(function () {
        Route::get('get/{title}', 'ContactSettingController@get');
        Route::put('edit', 'ContactSettingController@edit');
    });

    // Product Category
    Route::prefix('product-category')->group(function () {
        Route::post('list', 'ProductCategoryController@list');
        Route::post('add', 'ProductCategoryController@add');
        Route::get('get/{id}', 'ProductCategoryController@get');
        Route::put('edit', 'ProductCategoryController@edit');
        Route::delete('delete/{id}', 'ProductCategoryController@delete');
    });

    // Product Brand
    Route::prefix('product-brand')->group(function () {
        Route::post('list', 'ProductBrandController@list');
        Route::post('add', 'ProductBrandController@add');
        Route::get('get/{id}', 'ProductBrandController@get');
        Route::put('edit', 'ProductBrandController@edit');
        Route::delete('delete/{id}', 'ProductBrandController@delete');
    });
});
