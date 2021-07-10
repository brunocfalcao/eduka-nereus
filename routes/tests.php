<?php

use Eduka\NovaAdvancedUI\Mail\ThankYouForSubscribing;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Eduka Nereus testing routes. On loaded in "local" environments.
|--------------------------------------------------------------------------
|
*/

Route::get('/mail', function () {
    return new ThankYouForSubscribing();
});
