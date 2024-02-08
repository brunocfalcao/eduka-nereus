<?php

use Illuminate\Support\Facades\Route;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;

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

Route::get(
    'password/reset/{token}?email={email}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');

/*
Route::post(
    'password/reset',
    'Auth\ResetPasswordController@reset'
)->name('password.update');
*/

Route::get('/', function () {
    return 'hello world';
});
