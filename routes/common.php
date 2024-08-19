<?php

use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Nereus\Middleware\CanSeeEpisode;
use Eduka\Nereus\Middleware\WithCourseInSession;
use Eduka\Payments\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Show reset password form
Route::get(
    'password/reset/{token}?email={email}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');

// View an episode (episode / screencast player)
Route::get(
    'episode/{episode::uuid}',
    [EpisodeController::class, 'play']
)
    ->name('episode.play')
    ->middleware(CanSeeEpisode::class);

Route::get('purchase', CheckoutController::class)
    ->middleware(WithCourseInSession::class)
    ->middlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1')
    ->name('purchase.checkout');
