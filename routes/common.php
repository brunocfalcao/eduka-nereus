<?php

use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Services\Mail\Subscribers\SubscribedToCourseMail;
use Illuminate\Support\Facades\Route;

Route::get(
    'password/reset/{token}?email={email}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');

Route::get('/mailable/subscribed', function () {

    $subscriber = Subscriber::firstOrCreate([
        'email' => 'bruno.falcao@live.com',
        'course_id' => 1,
    ]);

    return new SubscribedToCourseMail($subscriber);
});
