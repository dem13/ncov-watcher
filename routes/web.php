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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'ncov'], function () {
    Route::get('crawler', 'NcovController@crawl');

    Route::get('chart/{field}', 'NcovController@chart')->name('chart');
});

Route::prefix('/telegram/' . config('telegram.bots.common.token'))->group(function () {
    Route::get('/webhook/setup', 'TelegramController@setupWebhook')->name('telegram.webhook.setup');
    Route::any('/update', 'TelegramController@update')->name('telegram.update');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'AdminController@panel')->name('admin.panel');
    Route::get('/global-message', 'AdminController@globalMessage')->name('admin.global_message');
});
