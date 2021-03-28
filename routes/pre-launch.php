<?php

use Eduka\Nereus\Controllers\PreLaunch\PreLaunchController;

/*
|--------------------------------------------------------------------------
| Pre-launch routes
|--------------------------------------------------------------------------
|
| These routes are loaded when your course is not yet launched.
| Mainly they will contain:
| - The pre-launch homepage route
| - The new subcription route
|
| Route example:
| Route::get('/posts/{post}/comments/{comment}', [PostsController::class, 'subscribe']);
*/

// Default welcome page.
Route::get('/', [PreLaunchController::class, 'welcome'])
     ->name('pre-launch.welcome');

Route::post('/', [PreLaunchController::class, 'subscribe'])
     ->name('pre-launch.subscribe');
