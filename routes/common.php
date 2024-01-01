<?php

use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\Http\Controllers\Auth\ForgotPasswordController;
use Eduka\Nereus\Http\Controllers\Auth\LoginController;
use Eduka\Nereus\Http\Controllers\Auth\RegisterController;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Nereus\Http\Controllers\HomeController;
use Eduka\Services\Mail\Subscribers\SubscribedToCourse;
use Illuminate\Support\Facades\Route;

Route::get('/mailable/subscribed', function () {
    return new SubscribedToCourse(Subscriber::firstWhere('id', 1));
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset']);

Route::get('/home', [HomeController::class, 'index'])->name('home');
