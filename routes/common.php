<?php

use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get(
    'password/reset/{token}?email={email}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');
