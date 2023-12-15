<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'backend::home')
     ->name('eduka.dev.home');

Route::view('/password/reset', 'backend::reset-password')
     ->name('eduka.dev.reset-password');
