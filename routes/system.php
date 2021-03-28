<?php

use Eduka\Nereus\Controllers\PostmarkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Eduka Nereus system routes. Always loaded.
|--------------------------------------------------------------------------
|
*/

Route::post('/inbound', [PostmarkController::class, 'inbound']);
