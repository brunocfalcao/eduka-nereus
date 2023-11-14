<?php

use App\Http\Middleware\VerifyCsrfToken;
use Eduka\Nereus\Http\Controllers\Launched;
use Eduka\Payments\Http\Controllers\PaymentController;
use Eduka\Payments\Http\Controllers\PaymentRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [Launched::class, 'welcome'])
    ->name('launched.welcome');

Route::get('purchase', [PaymentController::class, 'redirectToCheckoutPage'])
    ->middleware('throttle:payment')
    ->name('purchase.checkout');

Route::get('/redirect-callback/{nonce}', [PaymentRedirectController::class, 'index'])
    ->middleware('throttle:payment')
    ->name('purchase.callback');

Route::post('/lemonsqueezy/webhook', [PaymentController::class, 'handleWebhook'])
     ->name('purchase.webhook')
     ->withoutMiddleware([VerifyCsrfToken::class]);
