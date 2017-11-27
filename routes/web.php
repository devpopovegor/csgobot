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

Route::post('/handle', 'TelegramController@handle');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test', 'TelegramController@test')->name('test');
Route::get('/add-item', 'ApiController@addItem')->name('add-item');
Route::get('/get-list', 'ApiController@getList')->name('get-list');
Route::get('/get-patterns', 'ApiController@getPatterns')->name('get-patterns');