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

use Illuminate\Support\Facades\Route;

Route::view('welcome', 'welcome')->name('welcome');

Route::post('login', 'Auth\LoginController@login')->name('login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/', 'HomeController@index')->name('home');

Route::get('/clients', 'ClientsController@read');
Route::post('/clients', 'ClientsController@create');
Route::post('/clients/{id}', 'ClientsController@update');
Route::delete('/clients/{id}', 'ClientsController@delete');

Route::get('/users/{client_id?}', 'UserController@read');
Route::post('/users', 'UserController@create');
Route::post('/users/{id}', 'UserController@update');
Route::delete('/users/{id}', 'UserController@delete');

Route::get('/checkins/{clientId?}', 'CheckinController@read');
Route::post('/checkin/', 'CheckinController@create');
Route::post('/checkin/{id}', 'CheckinController@update');
Route::delete('/checkin/{id}', 'CheckinController@delete');

Route::get('/qrcode/{id}', 'UserController@getQrCode');

Route::get('/settings', 'SettingsController@read');
Route::post('/settings', 'SettingsController@update');
