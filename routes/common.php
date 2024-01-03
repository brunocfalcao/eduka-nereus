<?php

use Eduka\Cube\Models\Subscriber;
use Eduka\Services\Mail\Subscribers\SubscribedToCourse;
use Illuminate\Support\Facades\Route;

Route::get('/mailable/subscribed', function () {
    return new SubscribedToCourse(Subscriber::firstWhere('id', 1));
});
