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
    Route::get('/volunteerism/{uuid}', 'Web\PostController@show_volunteer_activity')->name('user.volunteerism.show');

    /**
     * jobs
     */
    Route::get('/work', 'Web\PostController@work')->name('user.work');
    Route::get('/work/{type_of_user}', 'Web\PostController@type_of_work')->name('user.work.type');
    Route::get('/work/{type_of_user}/{type_of_work}', 'Web\PostController@list_jobs')->name('user.work.jobs');

    /**
     * apply for job
     */
    Route::get('/posts/{uuid}/apply', 'Web\PostController@apply_for_job')->name('user.apply_for_job');

    /**
     * notifications
     */
    Route::get('/notifications', 'Web\UserController@get_user_notifications')->name('user.notifications');
    Route::get('/notifications/{notification_group_id}', 'Web\UserController@get_user_notification_details')->name('user.notifications.show');

    /**
     * wallet
     */
    Route::get('/wallet', 'Web\UserController@my_wallet')->name('user.wallet');

    /**
     * postings
     */
    Route::get('/posts', 'Web\UserController@user_postings')->name('user.posts.list');
    Route::get('/posts/{uuid}/edit', 'Web\UserController@user_posts')->name('user.posts.edit');
    Route::get('/posts/{uuid}/show', 'Web\PostController@show_user_post_details')->name('user.posts.show');

    Route::get('/posts/{application_id}/{action}', 'Web\PostController@confirm_decline_applicant')->name('user.posts.confirm_decline_applicant');

    /**
     * profile
     */
    Route::get('/profile/{user_id}', 'Web\UserController@user_profile')->name('user.profile');
});

Route::get('/login', 'Web\AuthController@show_login_form')->name('login');
Route::post('/login', 'Web\AuthController@login')->name('user.login');


require __DIR__.'/auth.php';
