<?php

use App\Http\Middleware\VerifyCsrfToken;
use Eduka\Cube\Models\Student;
use Eduka\Payments\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/webhook', WebhookController::class)
    ->name('payments.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/payments/webhook', function () {

    $student = Student::query()->latest()->first();

    $token = '12345';

    return
        dd(
            route_with_custom_domain(
                $student->courses->first()->backend->domain,
                'password.reset',
                [
                    'token' => $token,
                    'email' => urlencode($student->email),
                ]
            )
        );
})->withoutMiddleware([VerifyCsrfToken::class]);
