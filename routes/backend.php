<?php

use Eduka\Nereus\Http\Controllers\Auth\ForgotPasswordController;
use Eduka\Nereus\Http\Controllers\Auth\LoginController;
use Eduka\Nereus\Http\Controllers\Auth\RegisterController;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Nereus\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::view('/password/reset/{token}?email={email}', 'backend::auth.password.reset')
     ->name('password.reset');

Route::get('/', function () {
    return 'hello world';
});
