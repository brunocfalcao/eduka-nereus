<?php

use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Order;
use Eduka\Services\Mail\Orders\OrderCreatedForExistingUserMail;
use Eduka\Services\Mail\Orders\OrderCreatedForNewUserMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

Route::get('mailable/order-created-new-user', function () {

    $student = User::find(1);

    // Create a password reset token for the user.
    $token = Password::broker()->createToken($student);

    // Construct password reset url.
    $url = route(
        'password.reset',
        [
            'token' => $token,
            'email' => urlencode($student->email),
        ]
    );

    return new OrderCreatedForNewUserMail(
        $student,
        Order::find(1),
        $url
    );
});

Route::get('mailable/order-created-existing-user', function () {
    return new OrderCreatedForExistingUserMail(
        User::find(1),
        Order::find(1),
        $url = 'https://'.Course::find(1)->domain
    );
});
