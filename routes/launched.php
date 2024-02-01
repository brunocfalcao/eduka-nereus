<?php

use Brunocfalcao\Tokenizer\Middleware\CheckToken;
use Eduka\Nereus\Http\Controllers\Launched;
use Eduka\Payments\Http\Controllers\CheckoutController;
use Eduka\Payments\Http\Controllers\RedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [Launched::class, 'welcome'])
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:10,1') // 5 requests per minute.
    ->name('launched.welcome');

Route::get('purchase', CheckoutController::class)
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1') // 5 requests per minute.
    ->name('purchase.checkout');

Route::get('thanks-for-buying', RedirectController::class)
    ->middleware(CheckToken::class)
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:2,1') // 5 requests per minute.
    ->name('purchase.callback');
