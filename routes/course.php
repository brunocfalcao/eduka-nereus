<?php

use Eduka\Cube\Models\Course;
use Eduka\Nereus\Controllers\PreLaunch\PreLaunchController;
use Eduka\Nereus\Controllers\PreLaunch\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Eduka course routes - Routes list. DO NOT CHANGE THIS FILE.
|--------------------------------------------------------------------------
|
*/

if (! course()->is_active) {
    // Meaning the course (pre-launch or launch) are not active at all.
    Route::view('/', 'eduka::inactive.default');
}

if (course()->launched()) {
    // Meaning we are in full launch mode.
    Route::view('/', 'site::launched.default');
}

if (course()->is_active && ! course()->launched()) {
    // Meaning we are in prelaunched mode.
    Route::view('/', 'site::prelaunched.default')
          ->name('prelaunched.welcome');

    // When you a new subscription happens.
    Route::post('/', [PreLaunchController::class, 'subscribe'])
          ->name('prelaunched.subscribed');
}

/*
// Default welcome page.
Route::get('/', [PreLaunchController::class, 'welcome'])
     ->name('pre-launch.welcome');

Route::post('/', [SubscriptionController::class, 'store'])
     ->name('pre-launch.subscribe');
*/
