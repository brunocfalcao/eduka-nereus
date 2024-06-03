<?php

use App\Http\Middleware\VerifyCsrfToken;
use Eduka\Cube\Models\Student;
use Eduka\Payments\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/lemonsqueezy/webhook', WebhookController::class)
    ->name('purchase.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/lemonsqueezy/webhook', function () {

    $student = Student::query()->latest()->first();

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
