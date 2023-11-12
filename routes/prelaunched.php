<?php

use Eduka\Nereus\Http\Controllers\Prelaunched;
use Illuminate\Support\Facades\Route;

Route::get('/', [Prelaunched::class, 'welcome'])
    ->name('prelaunched.welcome');

Route::post('/', [Prelaunched::class, 'subscribe'])
    ->name('prelaunched.subscribe');
