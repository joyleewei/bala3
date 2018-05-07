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

Route::group(['namespace'=>'Home'],function(){
    Route::get('/','TopicsController@index')->name('home.pages.root');
    // 用户信息
    Route::get('/users/{user}','UsersController@show')->name('users.show');
    Route::get('/users/{user}/edit','UsersController@edit')->name('users.edit');
    Route::patch('/users/{user}','UsersController@update')->name('users.update');
    // 话题信息
    Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);
    // 显示话题
    Route::get('topics/{topic}/{slug?}','TopicsController@show')->name('topics.show');
    // 类别信息
    Route::resource('categories','CategoriesController',['only'=>['show']]);
    // 上传图片
    Route::post('upload_image','TopicsController@uploadImage')->name('topics.upload_image');
    // 回复
    Route::resource('replies', 'RepliesController', ['only' => [ 'store', 'destroy']]);
    // 通知消息
    Route::resource('notifications','NotificationsController',['only'=>['index']]);
    Route::get('permission-denied','PagesController@permissionDenied')->name('permission-denied');
});

// 用户认证模块
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');