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

Route::get('/', function () {
    return view('welcome');
});
//Route::get('/', 'Web\HomeController@index')->name('home');
//Route::get('/home', 'Web\HomeController@index')->name('home');

Route::group(['middleware'=>'auth:web'], function () {
    Route::get('/', 'Web\HomeController@index')->name('home');
    Route::get('/home', 'Web\HomeController@index')->name('home');
    Route::post('/logout', 'Web\AuthController@logout')->name('logout');

    Route::get('/volunteerism', 'Web\PostController@volunteerism')->name('user.volunteerism');
});

Route::get('/login', 'Web\AuthController@show_login_form')->name('login');
Route::post('/login', 'Web\AuthController@login')->name('user.login');

