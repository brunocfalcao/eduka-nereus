<?php

use App\Http\Middleware\VerifyCsrfToken;
use Eduka\Payments\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/lemonsqueezy/webhook', WebhookController::class)
    ->name('purchase.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);
