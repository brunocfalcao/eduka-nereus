<?php

use Eduka\Nereus\Http\Controllers\Launched;
use Eduka\Payments\Http\Controllers\PaymentController;
use Eduka\Payments\Http\Controllers\PaymentRedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [Launched::class, 'welcome'])
    ->name('launched.welcome');

Route::get('purchase', [PaymentController::class, 'redirectToCheckoutPage'])
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1') // 5 requests per minute.
    ->name('purchase.checkout');

Route::get('/thanks-for-buying', [PaymentRedirectController::class, 'thanksForBuying'])
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1') // 5 requests per minute.
    ->name('purchase.callback');
