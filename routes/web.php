<?php

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

//首页

Route::get('/','PagesController@root')->name('root');


// 身份验证 Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

Route::post('login', 'Auth\LoginController@login');

Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// 注册登记 Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');

//Route::post('register', 'Auth\RegisterController@register');

// 密码重置 Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::post('register', 'Auth\RegisterController@register');



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
