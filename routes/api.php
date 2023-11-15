<?php

use Illuminate\Support\Facades\Route;

Route::post('/lemonsqueezy/webhook', [PaymentController::class, 'handleWebhook'])
     ->name('purchase.webhook')
     ->withoutMiddleware([VerifyCsrfToken::class]);
