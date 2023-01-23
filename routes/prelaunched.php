<?php

use Eduka\Nereus\Http\Controllers\Prelaunched\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', [Welcome::class, 'index'])
      ->name('welcome.default');
