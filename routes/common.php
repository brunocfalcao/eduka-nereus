<?php

use Eduka\Cube\Events\Subscribers\SubscriberCreatedEvent;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Services\Mail\Subscribers\SubscribedToCourseMail;
use Illuminate\Support\Facades\Route;

Route::get(
    'password/reset/{token}?email={email}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');

Route::get('/mailable/subscribed', function () {

    Subscriber::firstWhere('email', env('EDUKA_SUBSCRIBER_TEST_EMAIL'))
        ->forceDelete();

    Subscriber::create([
        'email' => env('EDUKA_SUBSCRIBER_TEST_EMAIL'),
        'course_id' => Course::firstWhere('domain', parse_url(request()->fullUrl())['host'])->id,
    ]);

    //event(new SubscriberCreatedEvent($subscriber));

    return 'event triggered.';

    //return new SubscribedToCourseMail($subscriber);
});
