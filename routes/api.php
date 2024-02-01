<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Eduka\Payments\Http\Controllers\PaymentController;
use Eduka\Payments\Http\Controllers\WebhookController;

Route::post('/lemonsqueezy/webhook', WebhookController::class)
    ->name('purchase.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);
