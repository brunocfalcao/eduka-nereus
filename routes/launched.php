<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Eduka\Nereus\Middleware\WithCourse;
use Eduka\Nereus\Http\Controllers\Launched;
use Brunocfalcao\Tokenizer\Middleware\WithToken;
use Eduka\Payments\Http\Controllers\CheckoutController;
use Eduka\Payments\Http\Controllers\RedirectController;

Route::get('/', [Launched::class, 'welcome'])
    ->middleware('guest')
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:10,1')
    ->name('launched.welcome');

Route::get('purchase', CheckoutController::class)
    ->middleware(WithCourse::class)
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1')
    ->name('purchase.checkout');

Route::get('thanks-for-buying', RedirectController::class)
    ->middleware(WithToken::class)
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:2,1')
    ->name('purchase.callback');
