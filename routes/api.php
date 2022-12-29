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
            Route::post('/update-user-fcm-token', 'UserController@update_user_fcm_token');
        });

        Route::prefix('/auth')->group(function () {
            Route::post('/login', 'AuthController@login');
        });
    });

    Route::middleware("auth:sanctum")->group(function () {
        Route::prefix('/volunteer')->group(function () {
            Route::post('/create-post', 'PostController@create_volunteer_post');
        });

        Route::prefix('/quick-jobs')->group(function () {
            Route::post('/create-post', 'PostController@create_quick_job_post');
        });

        Route::prefix('/activities')->group(function () {
            Route::get('/get-user-posts', 'PostController@get_user_posts');
            Route::get('/get-posts', 'PostController@get_posts_closest_to_me');
            Route::get('/get-post-details', 'PostController@get_post_details');
            Route::post('/apply-for-job', 'PostController@apply_for_job');
            Route::get('/get-user-post-status', 'PostController@get_user_post_status');
            Route::post('/confirm-decline-applicant', 'PostController@confirm_decline_applicant');
            Route::post('/close-post', 'PostController@close_post');
            Route::get('/get-user-post-details', 'PostController@get_user_post_details');
        });

        Route::prefix('/profile')->group(function () {
            Route::post('/update-current-location', 'UserController@update_user_current_location');
            Route::post('/update-user-core-skills', 'UserController@update_user_core_skills');
        });

        Route::prefix('/config')->group(function () {
            Route::get('/get-skills', 'ConfigController@get_skills');
        });
    });
});
