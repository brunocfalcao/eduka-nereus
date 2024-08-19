<?php

use Eduka\Nereus\Http\Controllers\Auth\ForgotPasswordController;
use Eduka\Nereus\Http\Controllers\Auth\LoginController;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Nereus\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::redirect('/', 'login');

// Login form
Route::get(
    'login',
    [LoginController::class, 'showLoginForm']
)
    ->name('login');

// Execute login
Route::post(
    'login',
    [LoginController::class, 'login']
)
    ->middleware(ProtectAgainstSpam::class);

// Show the dashboard
Route::get(
    'home',
    [HomeController::class, 'index']
)
    ->middleware('auth')
    ->name('home');

// Show the student profile
Route::get(
    'profile',
    [HomeController::class, 'profile']
)
    ->middleware('auth')
    ->name('profile');

// Execute logout
Route::post(
    'logout',
    [LoginController::class, 'logout']
)
    ->name('logout');

// Show reset form, after clicking on the reset password email link
Route::get(
    'password/reset/{token}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');

// Submits a reset password that was just changed
Route::post(
    'password/reset',
    [ResetPasswordController::class, 'reset']
)->name('password.update');

// Show form to request a link to request the password
Route::get(
    'password/reset',
    [ForgotPasswordController::class, 'showLinkRequestForm']
)->name('password.request')
    ->middleware(ProtectAgainstSpam::class);

// ---------- TESTING PURPOSES ----

Route::get('/chapters/', function () {
    return view('backend::chapters');
});

Route::get('/course/enrolled/', function () {
    return view('backend::course-enrolled');
});

Route::get('/activity/', function () {
    return view('backend::activity');
});

Route::get('/profile/', function () {
    return view('backend::profile.profile', ['profile_page' => 'profile']);
});

Route::get('/billing/', function () {
    return view('backend::profile.billing', ['profile_page' => 'billing']);
});

// --------------------------------

/*
done:
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
done:
$this->post('login', 'Auth\LoginController@login');
done:
$this->post('logout', 'Auth\LoginController@logout')->name('logout');
done:
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
 */

/*
Route::get(
    'password/reset',
    'Auth\ForgotPasswordController@showLinkRequestForm'
)->name('password.request');

Route::post(
    'password/email',
    'Auth\ForgotPasswordController@sendResetLinkEmail'
)->name('password.email');
*/

/*
Route::post(
    'password/reset',
    'Auth\ResetPasswordController@reset'
)->name('password.update');
*/

/*
Route::get('/', function () {
    return 'hello world';
})->middleware('auth');
*/
