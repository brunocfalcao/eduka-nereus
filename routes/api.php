<?php

use App\Http\Middleware\VerifyCsrfToken;
use Eduka\Payments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/lemonsqueezy/webhook', [PaymentController::class, 'handleWebhook'])
    ->name('purchase.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);
