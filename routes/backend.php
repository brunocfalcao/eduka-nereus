<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'backend::default');

Route::post('/lemonsqueezy/webhook', [PaymentController::class, 'handleWebhook'])
     ->name('purchase.webhook')
     ->withoutMiddleware([VerifyCsrfToken::class]);
