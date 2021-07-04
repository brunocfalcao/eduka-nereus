<?php

use Eduka\Cube\Services\Course;
use Eduka\Nereus\Controllers\PreLaunch\PreLaunchController;
use Eduka\Nereus\Controllers\PreLaunch\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Eduka course routes
|--------------------------------------------------------------------------
|
*/

if (!Course::active()) {
     // Meaning the course (pre-launch or launch) are not active at all.
     Route::view('/', 'site::inactive.default');
}

if (Course::launched()) {
     // Meaning we are in full launch mode.
     Route::view('/', 'site::launched.default');
}

if (!Course::launched()) {
     // Meaning we are in pre-launch mode.
     Route::view('/', 'site::prelaunched.default');
     
     // When you subscribe a new email.
     Route::post('/', [PreLaunchController::class, 'subscribe'])
          ->name('subscribe');
}

/*
// Default welcome page.
Route::get('/', [PreLaunchController::class, 'welcome'])
     ->name('pre-launch.welcome');

Route::post('/', [SubscriptionController::class, 'store'])
     ->name('pre-launch.subscribe');
*/
