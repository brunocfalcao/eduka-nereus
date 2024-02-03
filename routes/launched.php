<?php

use Eduka\Nereus\Http\Controllers\Launched;
use Eduka\Nereus\Middleware\WithCourse;
use Eduka\Payments\Http\Controllers\CheckoutController;
use Eduka\Payments\Http\Controllers\RedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [Launched::class, 'welcome'])
    ->middleware('guest')
    ->middlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:10,1')
    ->name('launched.welcome');

Route::get('purchase', CheckoutController::class)
    ->middleware(WithCourse::class)
    ->middlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1')
    ->name('purchase.checkout');

Route::get('thanks-for-buying', RedirectController::class)
    ->middlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:2,1')
    ->name('purchase.callback');
