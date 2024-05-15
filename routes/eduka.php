<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'eduka::welcome')
    ->name('welcome');
