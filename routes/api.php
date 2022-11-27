<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/v1')->group(function () {
    //onboarding
    Route::middleware("unguarded")->group(function () {
        Route::prefix('/onboarding')->group(function () {
            Route::post('/register', 'OnboardingController@register');
            Route::post('/verify-phone-number', 'OnboardingController@verify_phone_number');
        });

        Route::prefix('/auth')->group(function () {
            Route::post('/login', 'AuthController@login');
        });
    });

    Route::middleware("auth:sanctum")->group(function () {
        Route::prefix('/volunteer')->group(function () {
            Route::post('/create-post', 'PostController@create_volunteer_post');
        });
    });
});
