<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>'auth'], function () {
    Route::get('/', 'Web\HomeController@index')->name('home');
    Route::get('/home', 'Web\HomeController@index')->name('home');
    Route::get('/dashboard', 'Web\HomeController@index')->name('home');
//    Route::post('/logout', 'Web\AuthController@logout')->name('logout');

    /*
     * volunteerism
     */
    Route::get('/volunteerism', 'Web\PostController@volunteerism')->name('user.volunteerism');
    Route::get('/volunteerism/upcoming-activities-near-you', 'Web\PostController@list_volunteer_activities')->name('user.volunteerism.list');
    Route::get('/volunteerism/organise', 'Web\PostController@create_volunteer_activity')->name('user.volunteerism.create');
    Route::post('/volunteerism/publish', 'Web\PostController@create_volunteer_post')->name('user.volunteerism.publish');
    Route::get('/volunteerism/{uuid}', 'Web\PostController@show_volunteer_activity')->name('user.volunteerism.show');

    /**
     * jobs
     */
    Route::get('/work', 'Web\PostController@work')->name('user.work');
    Route::post('/work/quick-jobs/publish', 'Web\PostController@create_quick_job_post')->name('user.quick_jobs.publish');
    Route::get('/work/{type_of_user}', 'Web\PostController@type_of_work')->name('user.work.type');
    Route::get('/gigs/{uuid}', 'Web\PostController@show_quick_job')->name('user.quick_job.show');
    Route::get('/work/{type_of_user}/{type_of_work}', 'Web\PostController@list_jobs')->name('user.work.jobs');

    /**
     * apply for job
     */
    Route::get('/posts/{uuid}/apply', 'Web\PostController@apply_for_job')->name('user.apply_for_job');

    /**
     * notifications
     */
    Route::get('/notifications', 'Web\UserController@get_user_notifications')->name('user.notifications');
    Route::get('/notifications/{notification_group_id}/show', 'Web\UserController@get_user_notification_details')->name('user.notifications.show');

    /**
     * wallet
     */
    Route::get('/wallet', 'Web\UserController@my_wallet')->name('user.wallet');

    /**
     * postings
     */
    Route::get('/posts', 'Web\UserController@user_postings')->name('user.posts.list');
    Route::get('/posts/{uuid}/edit', 'Web\PostController@updateUserPost')->name('user.posts.edit');
    Route::get('/posts/{uuid}/show', 'Web\PostController@show_user_post_details')->name('user.posts.show');
    Route::post('/posts/{uuid}/update', 'Web\PostController@updatePostInformation')->name('user.posts.update');
    Route::post('/close-post', 'Web\PostController@close_post')->name("user.posts.close");

    Route::get('/posts/{application_id}/{action}', 'Web\PostController@confirm_decline_applicant')->name('user.posts.confirm_decline_applicant');

    /**
     * profile
     */
    Route::get('/profile/{user_id}', 'Web\UserController@user_profile')->name('user.profile');

    /**
     * onboarding
     */
    Route::post('/onboarding/location', 'Web\UserController@update_user_location')->name('onboarding.location.update');
    Route::post('/onboarding/skills-and-interest', 'Web\UserController@update_user_core_skills')->name('onboarding.skills_and_interest.update');
    Route::post('/onboarding/languages', 'Web\UserController@update_user_languages')->name('onboarding.languages.update');

    /**
     * trainings
     */
    Route::get('/training/recommended', 'Web\TrainingController@index')->name('training.index');

});

/**
 * Login
 */
Route::get('/login', 'Web\AuthController@show_login_form')->name('login');
Route::post('/login', 'Web\AuthController@login')->name('user.login');

/**
 * register
 */
Route::get('/register', 'Web\OnboardingController@registration_form')->name('onboarding.register');
Route::post('/register', 'Web\OnboardingController@register')->name('onboarding.register.submit');
Route::get('/register/{uuid}/verify-phone-number', 'Web\OnboardingController@verify_onboarding_phone_number')->name('onboarding.verify_phone_number');
Route::post('/register/verify-phone-number', 'Web\OnboardingController@verify_phone_number')->name('onboarding.verify_phone_number.submit');

Route::get('/auth/otp/resend/{user}', 'Web\OnboardingController@resend_otp')->name('auth.resend_otp');

require __DIR__.'/auth.php';
