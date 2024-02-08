<?php

use Eduka\Cube\Models\Subscriber;
use Illuminate\Support\Facades\Route;
use Eduka\Services\Mail\Subscribers\SubscribedToCourse;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;

Route::get('/mailable/subscribed', function () {
    return new SubscribedToCourse(Subscriber::firstWhere('id', 1));
});

Route::get(
    'password/reset/{token}?email={email}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');
