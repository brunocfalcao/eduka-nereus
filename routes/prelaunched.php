<?php

use Eduka\Nereus\Http\Controllers\Prelaunched;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', [Prelaunched::class, 'welcome'])
    ->name('prelaunched.welcome');

Route::post('/', [Prelaunched::class, 'subscribe'])
    ->middleware(ProtectAgainstSpam::class)
    ->name('prelaunched.subscribe');
