<?php

use Eduka\Nereus\Http\Controllers\Launched;
use Eduka\Payments\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [Launched::class, 'welcome'])
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:10,1') // 5 requests per minute.
    ->name('launched.welcome');

Route::get('purchase', [PaymentController::class, 'redirectToCheckoutPage'])
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1') // 5 requests per minute.
    ->name('purchase.checkout');

Route::get('/thanks-for-buying', [PaymentController::class, 'thanksForBuying'])
    ->withMiddlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:2,1') // 5 requests per minute.
    ->name('purchase.callback');
