<?php

use Eduka\Cube\Models\Student;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Eduka\Payments\Http\Controllers\WebhookController;

Route::post('/lemonsqueezy/webhook', WebhookController::class)
    ->name('purchase.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/lemonsqueezy/webhook', function () {

    $student = Student::all()->last();
    $token = '12345';

    return
        dd(
            eduka_route(
                $student->courses->first()->backend->domain,
                'password.reset',
                [
                    'token' => $token,
                    'email' => urlencode($student->email),
                ]
            )
        );
})->withoutMiddleware([VerifyCsrfToken::class]);
