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
            Route::post('/register', 'Mobile\OnboardingController@register');
            Route::post('/verify-phone-number', 'Mobile\OnboardingController@verify_phone_number');
        });

        Route::prefix('/auth')->group(function () {
            Route::post('/login', 'Mobile\AuthController@login');
            Route::post('/get-password-reset-otp', 'Mobile\AuthController@passwordResetPhoneNumberVerification');
            Route::post('/verify-password-reset-otp', 'Mobile\AuthController@verifyOTPForPasswordReset');
            Route::post('/reset-password', 'Mobile\AuthController@executePasswordReset');
        });
    });

    Route::middleware("auth:sanctum")->group(function () {
        Route::prefix('/volunteer')->group(function () {
            Route::post('/create-post', 'Mobile\PostController@create_volunteer_post');
            Route::post('/update-post', 'Mobile\PostController@updatePost');
        });

        Route::prefix('/quick-jobs')->group(function () {
            Route::post('/create-post', 'Mobile\PostController@create_quick_job_post');
            Route::post('/update-post', 'Mobile\PostController@updatePost');
        });

        Route::prefix('/fixed-term-jobs')->group(function () {
            Route::post('/create-post', 'Mobile\PostController@create_fixed_term_job_post');
            Route::post('/update-post', 'Mobile\PostController@updatePost');
        });

        Route::prefix('/permanent-jobs')->group(function () {
            Route::post('/create-post', 'Mobile\PostController@create_permanent_job_post');
            Route::post('/update-post', 'Mobile\PostController@updatePost');
        });

        Route::prefix('/activities')->group(function () {
            Route::post('/get-user-posts', 'Mobile\PostController@get_user_posts');
            Route::post('/get-posts', 'Mobile\PostController@get_posts_closest_to_me');
            Route::get('/get-posts', 'Mobile\PostController@get_posts_closest_to_me');
            Route::get('/get-post-details', 'Mobile\PostController@get_post_details');
            Route::post('/apply-for-job', 'Mobile\PostController@apply_for_job');
            Route::post('/get-user-post-status', 'Mobile\PostController@get_user_post_status');
            Route::post('/confirm-decline-applicant', 'Mobile\PostController@confirm_decline_applicant');
            Route::post('/close-post', 'Mobile\PostController@close_post');
            Route::get('/get-user-post-details', 'Mobile\PostController@get_user_post_details');
            Route::post('/get-user-notifications', 'Mobile\UserController@get_user_notifications');
            Route::post('/get-user-notification-details', 'Mobile\UserController@get_user_notification_details');
            Route::get('/get-user-notifications-count', 'Mobile\UserController@get_user_unread_notifications_count');
        });

        Route::prefix('/profile')->group(function () {
            Route::post('/update-current-location', 'Mobile\UserController@update_user_current_location');
            Route::post('/update-user-core-skills', 'Mobile\UserController@update_user_core_skills');
            Route::post('/update-user-languages', 'Mobile\UserController@update_user_languages');
            Route::post('/update-user-fcm-token', 'Mobile\UserController@update_user_fcm_token');
            Route::get('/kyc', 'Mobile\UserController@get_user_kyc_status');
            Route::get('/', 'Mobile\UserController@get_user_full_profile');
            Route::get('/job-history', 'Mobile\UserController@get_job_history');

            Route::post('/update-user-display-name', 'Mobile\UserController@updateProfileInformation');
            Route::post('/update-user-password', 'Mobile\UserController@updateProfileInformation');

            /**
             * other user profile routes
             */
            Route::get('/get-user-vork-profile', 'Mobile\UserController@get_user_vork_profile');
            Route::get('/is-toolbox-user', 'Mobile\UserController@isToolboxUser');

        });

        Route::prefix('/config')->group(function () {
            Route::post('/get-skills', 'Mobile\ConfigController@get_skills');
            Route::post('/get-languages', 'Mobile\ConfigController@get_languages');
        });

        Route::prefix('/user-accounts')->group(function () {
            Route::get('/deleteAccount', 'Mobile\UserController@deleteAccount');
        });
    });
});
