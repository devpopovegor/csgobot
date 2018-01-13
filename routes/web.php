<?php

Route::get('/', function () {
    return view('welcome');
});

Route::post('/handle', 'TelegramController@handle');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'test'], function()
{
	Route::get('set-steam-task/{id}', 'TestController@set_steams_task');
	Route::get('set-patterns-name', 'TestController@set_patterns_name');
	Route::get('get-patterns', 'TestController@get_patterns');
	Route::get('delete-patterns', 'TestController@delete_patterns');
	Route::get('set-patterns', 'TestController@set_patterns');
	Route::get('get-tasks/{site_id}/{username}', 'TestController@get_tasks');
	Route::get('get-items', 'TestController@get_items');
	Route::get('insert/{site_id}', 'TestController@insert_paintseed_task');
	Route::get('delete/{site_id}', 'TestController@delete_paintseed_task');
	Route::get('delete-tasks/{username}', 'TestController@delete_user_tasks');
});

Route::group(['prefix' => 'api'], function()
{
	Route::get('add-item', 'ApiController@addItem');
	Route::get('tasks', 'ApiController@getTasks');
	Route::get('patterns', 'ApiController@getPatterns');
	Route::get('set-patterns', 'ApiController@setPatterns');
	Route::get('steam', 'ApiController@getSteam');
	Route::get('send', 'ApiController@send');
	Route::get('send-telegram/{client}/{name}/{float}/{pattern}/{metjm}/{item_id}', 'ApiController@sendTelegram');

});