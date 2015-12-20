<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/receive/{token}', 'TelegramController@receive');
Route::get('/token/{clientId}', 'TokenController@generateToken');
Route::get('/login', 'UserController@login');
Route::post('/user', 'AuthController@code');

//Route::group(['middleware' => ['auth']], function()
//{

//});
