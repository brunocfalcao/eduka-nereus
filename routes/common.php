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

oute::get('/mailable/subscribed/{method}', function ($method) {
    // You can now use the $method variable in your logic
    if (!in_array($method, ['queue', 'sync'])) {
        return response('Invalid method', 400);
    }

    $subscriberEmail = env('EDUKA_SUBSCRIBER_TEST_EMAIL');
    $subscriber = Subscriber::firstWhere('email', $subscriberEmail);

    if ($subscriber) {
        $subscriber->forceDelete();
    }

    $course = Course::firstWhere('domain', parse_url(request()->fullUrl())['host']);
    if ($course) {
        $subscriber = Subscriber::create([
            'email' => $subscriberEmail,
            'course_id' => $course->id,
        ]);

        // Optionally trigger the event based on the method
        if ($method === 'queue') {
            // Dispatch the event to the queue
            event(new \App\Events\SubscriberCreatedEvent($subscriber));
        } else {
            // Trigger the event synchronously
            \App\Events\SubscriberCreatedEvent::dispatchSync($subscriber);
        }

        return 'Event triggered using ' . $method . '.';
    } else {
        return response('Course not found', 404);
    }
});
