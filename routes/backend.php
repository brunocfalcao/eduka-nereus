<?php

use Eduka\Nereus\Http\Controllers\Auth\ForgotPasswordController;
use Eduka\Nereus\Http\Controllers\Auth\LoginController;
use Eduka\Nereus\Http\Controllers\Auth\RegisterController;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Nereus\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'backend::home')
    ->name('eduka.dev.home');

//
//Route::view('/password/reset/{token}?email={email}', 'backend::auth.password.reset')
//     ->name('eduka.dev.reset-password');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('eduka.dev.login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('eduka.dev.logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('eduka.dev.register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('eduka.dev.password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('eduka.dev.password.email');

//@note the route name is used 'password.reset' because the laravel/ui
// package has a hardcoded route name inside the notification
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/home', [HomeController::class, 'index'])->name('eduka.dev.home');
