<?php

use Eduka\Nereus\Http\Controllers\NewsletterController;
use Eduka\Nereus\Http\Controllers\Prelaunched\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', [Welcome::class, 'index'])
    ->name('welcome.default');

Route::post('/subscribe-to-newsletter', [NewsletterController::class, 'subscribeToNewsletter'])
    ->name('subscribe.newsletter');
