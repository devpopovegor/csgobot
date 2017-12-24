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
	Route::get('set_patterns', 'TestController@set_patterns');
});

Route::group(['prefix' => 'api'], function()
{
	Route::get('add-item', 'ApiController@addItem');
	Route::get('tasks', 'ApiController@getTasks');
	Route::get('patterns', 'ApiController@getPatterns');
	Route::get('set-patterns', 'ApiController@setPatterns');
	Route::get('steam', 'ApiController@setPatterns');

});