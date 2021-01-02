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

Route::middleware(['guest'])->group(function() {

    Route::view('welcome', 'welcome')->name('welcome');
    Route::view('send-reset', 'auth.passwords.send-reset');
    Route::post('forgot-password', 'Auth\ResetPasswordController@forgotPassword');
    Route::get('reset-password/{token}', 'Auth\ResetPasswordController@showResetForm')
        ->name('password.reset');
    Route::post('reset-password', 'Auth\ResetPasswordController@reset');
    Route::view('signup', 'signup')->name('signup');
    Route::post('payments', 'PaymentController@handle')->name('payments');
});

Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('signup', 'Auth\RegisterController@signup');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::middleware(['auth:web'])->group(function () {

    Route::get('/', 'HomeController@index')->middleware('payment.method')->name('home');

    Route::get('paymentmethod', 'HomeController@paymentMethod')->name('payment_method');

    Route::get('/clients', 'ClientsController@read');
    Route::post('/clients', 'ClientsController@create');
    Route::post('/clients/{id}', 'ClientsController@update');
    Route::delete('/clients/{id}', 'ClientsController@delete');

    Route::get('/users/{client_id?}', 'UserController@read');
    Route::get('/user', 'UserController@getLoggedInUser');
    Route::post('/users', 'UserController@create');
    Route::post('/users/{id}', 'UserController@update');
    Route::delete('/users/{id}', 'UserController@delete');

    Route::get('/checkins/{clientId?}', 'CheckinController@read');
    Route::post('/checkin/', 'CheckinController@create');
    Route::post('/checkin/{id}', 'CheckinController@update');
    Route::delete('/checkin/{id}', 'CheckinController@delete');

    Route::get('/qrcode/{id}', 'UserController@getQrCode');

    Route::get('/settings/{client_id}', 'SettingsController@read');
    Route::post('/settings/{id}', 'SettingsController@update');

    Route::get('/events/{client_id}/{event_id?}', 'EventController@read');
    Route::post('/event/', 'EventController@create');
    Route::post('/event/{id}', 'EventController@update');
    Route::delete('/event/{id}', 'EventController@delete');

    Route::post('/feedback/', 'MessageController@feedback');
    Route::get('/eligible/{userId}', 'MessageController@eligibleForPromo');

    Route::get('/customer', 'PaymentController@findOrCreateCustomer');
    Route::get('/payment_methods', 'PaymentController@getPaymentMethods');
    Route::post('/payment_method', 'PaymentController@setDefaultPaymentMethod');
    Route::delete('payment_method/{id}', 'PaymentController@deletePaymentMethod');
    Route::post('/subscription', 'PaymentController@upsertSubscription');
});
