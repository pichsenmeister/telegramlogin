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

// webhook for all incoming telegram requests
Route::post('/receive/{token}', 'TelegramController@receive');

// login / signup route for TelegramLogin.com
Route::get('/login', 'UserController@login');

// route to exchange code to access token
Route::post('/code', 'CodeController@code');
Route::get('/code', 'CodeController@code');

// generate token and redirect to telegram.me site
Route::get('/token/{clientId}', 'TokenController@generateToken');

Route::group(['middleware' => ['auth']], function()
{
    Route::get('app', 'AppController@index');
});
