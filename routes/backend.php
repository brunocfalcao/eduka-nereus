<?php

use Eduka\Nereus\Http\Controllers\Auth\LoginController;
use Eduka\Nereus\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::redirect('/', 'login');

// Login form
Route::get('login', [LoginController::class, 'showLoginForm'])
    ->name('login');

// Execute login
Route::post('login', [LoginController::class, 'login'])
    ->middleware(ProtectAgainstSpam::class);

// Show the dashboard
Route::get('home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

// Execute logout
Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout');

/*
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');

$this->post('logout', 'Auth\LoginController@logout')->name('logout');

$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
 */

/*
Route::get(
    'password/reset',
    'Auth\ForgotPasswordController@showLinkRequestForm'
)->name('password.request');

Route::post(
    'password/email',
    'Auth\ForgotPasswordController@sendResetLinkEmail'
)->name('password.email');
*/

/*
Route::post(
    'password/reset',
    'Auth\ResetPasswordController@reset'
)->name('password.update');
*/

/*
Route::get('/', function () {
    return 'hello world';
})->middleware('auth');
*/
