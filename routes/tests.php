<?php

use Eduka\Nereus\Controllers\Tests\TestMailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Test route
|--------------------------------------------------------------------------
|
| Just some test routes for eduka.
|
*/

Route::get('/tests/mail', [TestMailController::class, 'TestThankYouForSubscribing']);
