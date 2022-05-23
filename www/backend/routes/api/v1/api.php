<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'abc';
});

Route::group([
    'middleware' => ['guest'],
], function () {
    Route::prefix('auth')->namespace('Auth')->group(function () {
        Route::post('register', 'RegisterController@action');
        Route::post('register/request_otp', 'RegisterController@requestOtp');
        Route::put('register/resend_otp', 'RegisterController@resendOtp');
        Route::put('register/verify_otp', 'RegisterController@verifyOtp');
        Route::post('login/user', 'LoginController@action');
        Route::post('password/reset/request_otp', 'ForgotPasswordController@sendOtp');
        Route::put('password/reset/resend_otp', 'ForgotPasswordController@resendOtp');
        Route::put('password/reset/verify_otp', 'ForgotPasswordController@verifyOtp');
        Route::post('password/reset', 'ForgotPasswordController@resetPassword');
    });
});

Route::group([
    'middleware' => ['auth:api', 'user_platform'],
], function () {
    Route::prefix('user')->namespace('Users')->group(function () {
        Route::apiResources([
            'address' => 'AddressController',
        ]);

        Route::prefix('primary_address')->group(function () {
            Route::get('default', 'AddressController@primaryAddress');
            Route::put('default/{address}', 'AddressController@editPrimaryAddress');
        });

        Route::post('refresh_token', 'UserController@userRefreshToken');
        Route::patch('logout', 'UserController@userLogout');
    });
});

Route::group([
    'middleware' => ['user_platform'],
], function () {
    Route::prefix('country')->namespace('Countries')->group(function () {
        Route::get('menu', 'CountryController@index');
        Route::get('state_menu', 'CountryController@getState');
        Route::get('city_menu', 'CountryController@getCity');
    });
});