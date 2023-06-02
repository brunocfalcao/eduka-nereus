<?php

use Eduka\Nereus\Http\Controllers\Prelaunched\Welcome;
use Eduka\Nereus\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [Welcome::class, 'index'])
      ->name('welcome.default');

// @todo make it post
Route::get('/subscribe-to-newsletter',[NewsletterController::class,'subscribeToNewsletter'])
    ->name('subscribe.newsletter');
