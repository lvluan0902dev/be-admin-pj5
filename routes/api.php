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

Route::namespace('Admin')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('product/upload-product-image/{id}', 'ProductController@uploadProductImage');

        Route::group([
            'middleware' => 'jwt.auth',
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
                Route::get('get-all', 'ProductCategoryController@getAll');
            });

            // Product Brand
            Route::prefix('product-brand')->group(function () {
                Route::post('list', 'ProductBrandController@list');
                Route::post('add', 'ProductBrandController@add');
                Route::get('get/{id}', 'ProductBrandController@get');
                Route::put('edit', 'ProductBrandController@edit');
                Route::delete('delete/{id}', 'ProductBrandController@delete');
                Route::get('get-all', 'ProductBrandController@getAll');
            });

            // Staff
            Route::prefix('staff')->group(function () {
                Route::post('list', 'StaffController@list');
                Route::post('add', 'StaffController@add');
                Route::get('get/{id}', 'StaffController@get');
                Route::put('edit', 'StaffController@edit');
                Route::delete('delete/{id}', 'StaffController@delete');
            });

            // Product
            Route::prefix('product')->group(function () {
                Route::post('list', 'ProductController@list');
                Route::post('add', 'ProductController@add');
                Route::get('get/{id}', 'ProductController@get');
                Route::put('edit', 'ProductController@edit');
                Route::delete('delete/{id}', 'ProductController@delete');
                Route::post('product-image-list/{id}', 'ProductController@productImageList');
                Route::delete('product-image-delete/{id}', 'ProductController@productImageDelete');
                Route::post('product-option-list/{id}', 'ProductController@productOptionList');
                Route::post('product-option-add/{id}', 'ProductController@productOptionAdd');
                Route::put('product-option-edit', 'ProductController@productOptionEdit');
                Route::delete('product-option-delete/{id}', 'ProductController@productOptionDelete');
                Route::get('product-option-get/{id}', 'ProductController@productOptionGet');
            });

            // Contact Manage
            Route::prefix('contact-manage')->group(function () {
                Route::post('list-message', 'ContactManageController@listMessage');
                Route::delete('delete-message/{id}', 'ContactManageController@deleteMessage');
                Route::post('list-notification-email', 'ContactManageController@listNotificationEmail');
                Route::delete('delete-notification-email/{id}', 'ContactManageController@deleteNotificationEmail');
            });

            // Blog
            Route::prefix('blog')->group(function () {
                Route::post('list', 'BlogController@list');
                Route::post('add', 'BlogController@add');
                Route::get('get/{id}', 'BlogController@get');
                Route::put('edit', 'BlogController@edit');
                Route::delete('delete/{id}', 'BlogController@delete');
            });

            // Blog Category
            Route::prefix('blog-category')->group(function () {
                Route::post('list', 'BlogCategoryController@list');
                Route::post('add', 'BlogCategoryController@add');
                Route::get('get/{id}', 'BlogCategoryController@get');
                Route::put('edit', 'BlogCategoryController@edit');
                Route::delete('delete/{id}', 'BlogCategoryController@delete');
                Route::get('get-all', 'BlogCategoryController@getAll');
            });
        });
    });
});

Route::namespace('Front')->group(function () {
    Route::prefix('front')->group(function () {
        Route::get('get-all-slider', 'HomeController@getAllSlider');
        Route::get('get-all-testimonial', 'HomeController@getAllTestimonial');
        Route::get('get-all-beauty-image', 'HomeController@getAllBeautyImage');
        Route::get('get-all-staff', 'AboutUsController@getAllStaff');
        Route::get('get-all-contact-setting', 'ContactUsController@getAllContactSetting');
        Route::post('send-message', 'ContactUsController@sendMessage');
        Route::post('register-notification-email', 'ContactUsController@registerNotificationEmail');
        Route::get('get-all-faq', 'FaqController@getAllFaq');
        Route::get('get-products-latest', 'HomeController@getProductsLatest');
        Route::get('get-most-viewed-products', 'HomeController@getMostViewedProducts');
        Route::get('get-all-product-category', 'ShopController@getAllProductCategory');
        Route::get('get-all-product-brand', 'ShopController@getAllProductBrand');
        Route::post('shop', 'ShopController@shop');
        Route::get('get-product/{url}', 'ShopController@getProduct');
    });
});
