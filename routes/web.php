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

//个人页面 Routes..

Route::get('/users/{user}','UsersController@show')->name('users.show');

Route::get('/users/{user}/edit','UsersController@edit')->name('users.edit');

Route::put('/users/{user}', 'UsersController@update')->name('users.update');

//话题页面操作
Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);

//话题页面
Route::get('topics/{topic}/{slug?}','TopicsController@show')->name('topics.show');

//话题页面分类
Route::resource('categories', 'CategoriesController', ['only' => ['show']]);

//上传图片
Route::post('upload_image','TopicsController@uploadImage')->name('topics.upload_image');

//话题回复
Route::resource('replies', 'RepliesController', ['only' => ['store','destroy']]);

Route::resource('replies', 'RepliesController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);

//消息通知
Route::resource('notifications', 'NotificationsController', ['only' => ['index']]);