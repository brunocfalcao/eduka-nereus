<?php

use Eduka\Nereus\Http\Controllers\Prelaunched;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [Prelaunched::class, 'welcome'])
    ->name('prelaunched.welcome');

Route::post('/', [Prelaunched::class, 'subscribe'])
    ->middleware(ProtectAgainstSpam::class)
    ->name('prelaunched.subscribe');

// Dump routes for debugging
Route::get('/route/list', function () {
    Artisan::call('route:list');
    $output = Artisan::output();
    return nl2br($output);
})->name('route.list');
