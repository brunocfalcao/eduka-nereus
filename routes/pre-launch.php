<?php

use Eduka\Nereus\Controllers\Tests\PostsController;

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
*/

/*
 * The pre-launch welcome page. When you are still in a pre-launch phase
 * this should be your main view.
 **/
Route::view('/', 'eduka::pre-launch.welcome');

Route::get('/posts/{post}/comments/{comment}', [PostsController::class, 'subscribe']);
