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
    Route::post('/work/fixed-term-jobs/publish', 'Web\PostController@create_fixed_term_job_post')->name('user.fixed_term_jobs.publish');
    Route::post('/work/permanent-jobs/publish', 'Web\PostController@create_permanent_job_post')->name('user.permanent_jobs.publish');
    Route::get('/work/{type_of_user}', 'Web\PostController@type_of_work')->name('user.work.type');
    Route::get('/gigs/{uuid}', 'Web\PostController@show_quick_job')->name('user.quick_job.show');
    Route::get('/part-time-jobs/{uuid}', 'Web\PostController@show_fixed_term_job_details')->name('user.show_fixed_term_job_details.show');
    Route::get('/permanent-jobs/{uuid}', 'Web\PostController@show_permanent_job_details')->name('user.show_permanent_job_details.show');
    Route::get('/work/{type_of_user}/{type_of_work}', 'Web\PostController@list_jobs')->name('user.work.jobs');


//   Route::group(["prefix" => "/api/a0abaee2-d6c2-4f06-9c14-24c4da21eac9"], function () {
       Route::get('/getFixedTermOpportunities', 'Web\PostController@getFixedTermOpportunities')->name('work.getFixedTermOpportunities');
       Route::get('/getCategories', 'Web\PostController@getCategories')->name('work.getCategories');
       Route::get('/getFixedTermOpportunityDetails/{uuid}', 'Web\PostController@getFixedTermOpportunityDetails')->name('work.getFixedTermOpportunityDetails');
       Route::get('/getFixedTermOpportunitiesBySearchRadius/{radius}', 'Web\PostController@getFixedTermOpportunitiesBySearchRadius')->name('work.getFixedTermOpportunitiesBySearchRadius');
//   });


    Route::get('/getPermanentOpportunities', 'Web\PostController@getPermanentOpportunities')->name('work.getPermanentOpportunities');
    Route::get('/getCategories', 'Web\PostController@getCategories')->name('work.getCategories');
    Route::get('/getPermanentOpportunityDetails/{uuid}', 'Web\PostController@gePermanentOpportunityDetails')->name('work.getPermanentOpportunityDetails');
    Route::get('/getPermanentOpportunitiesBySearchRadius/{radius}', 'Web\PostController@getPermanentOpportunitiesBySearchRadius')->name('work.getPermanentOpportunitiesBySearchRadius');

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
    Route::get('/posts/{uuid}/remove-post', 'Web\PostController@removePost')->name("user.posts.remove");

    Route::get('/posts/{application_id}/{action}', 'Web\PostController@confirm_decline_applicant')->name('user.posts.confirm_decline_applicant');

    /**
     * onboarding
     */
    Route::post('/onboarding/location', 'Web\UserController@update_user_location')->name('onboarding.location.update');
    Route::get('/profile/update-user-location', 'Web\UserController@set_location')->name('user.profile.updateLocation');
    Route::get('/profile/update-skills-and-interest', 'Web\UserController@setSkillsAndInterest')->name('user.profile.skills_and_interest');
    Route::get('/profile/update-languages', 'Web\UserController@setLanguages')->name('user.profile.languages');
    Route::post('/onboarding/skills-and-interest', 'Web\UserController@update_user_core_skills')->name('onboarding.skills_and_interest.update');
    Route::post('/onboarding/languages', 'Web\UserController@update_user_languages')->name('onboarding.languages.update');

    /**
     * profile
     */
    Route::post('/profile/update/{module}', 'Web\UserController@updateProfileInformation')->name('user.updateProfileInformation');
    Route::get('/profile/change-my-password', 'Web\UserController@updatePassword')->name('user.updatePassword');
    Route::get('/profile/{user_id}', 'Web\UserController@user_profile')->name('user.profile');

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

/**
 * forgot password
 */
Route::get('auth/forgot-password', 'Web\AuthController@forgotPassword')->name('auth.forgotPassword');
Route::post('auth/forgot-password', 'Web\AuthController@verifyAccount')->name('auth.verifyAccount.submit');
Route::get('/auth/password-reset/{uuid}/confirm-password-reset', 'Web\AuthController@confirmPasswordReset')->name('auth.password_reset.verify_phone_number');
Route::post('/auth/password-reset/confirm-password-reset', 'Web\AuthController@confirmPasswordResetViaCode')->name('auth.password_reset.confirmPasswordReset');
Route::get('/auth/password-reset/resend-code/{user}', 'Web\AuthController@resendCode')->name('auth.password_reset.resendCode');
Route::post('/auth/password-reset', 'Web\AuthController@executePasswordReset')->name('auth.password_reset.executePasswordReset');
Route::get('/auth/{user}/password-reset', 'Web\AuthController@readyPasswordReset')->name('auth.passwordReset.ready');

Route::get('/auth/reload-captcha', 'Web\OnboardingController@reloadCaptcha')->name('captcha.reload');


require __DIR__.'/auth.php';
