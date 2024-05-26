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

Route::get('/mailable/subscribed/{method?}', function ($method = 'sync') {

    $subscriberEmail = env('EDUKA_SUBSCRIBER_TEST_EMAIL');
    $subscriber = Subscriber::firstWhere('email', $subscriberEmail);

    if ($subscriber) {
        $subscriber->forceDelete();
    }

    $course = Course::firstWhere('domain', parse_url(request()->fullUrl())['host']);
    if ($course) {
        $subscriber = Subscriber::withoutEvents(function () use ($subscriberEmail, $course) {
            return Subscriber::create([
                'email' => $subscriberEmail,
                'course_id' => $course->id,
            ]);
        });

        if ($method === 'queue') {
            // Dispatch the event to the queue
            event(new SubscriberCreatedEvent($subscriber));

            return 'Event triggered using queue.';
        } else {
            // Return the mailable directly to the webpage
            return new SubscribedToCourseMail($subscriber);
        }
    } else {
        return response('Course not found', 404);
    }
});
