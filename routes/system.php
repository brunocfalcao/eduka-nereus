<?php

use Illuminate\Support\Facades\Route;
use Eduka\Nereus\Controllers\PostmarkController;

/*
|--------------------------------------------------------------------------
| Eduka Nereus system routes. Always loaded.
|--------------------------------------------------------------------------
|
*/

Route::post('/inbound', [PostmarkController::class, 'inbound']);
