<?php

use Eduka\Nereus\Http\Controllers\Auth\ResetPasswordController;
use Eduka\Nereus\Middleware\CanSeeEpisode;
use Eduka\Nereus\Middleware\WithCourseInSession;
use Eduka\Payments\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Eduka\Nereus\Http\Controllers\EpisodePageController;
use Eduka\Cube\Models\Episode;

// Show reset password form
Route::get(
    'password/reset/{token}?email={email}',
    [ResetPasswordController::class, 'showResetForm']
)->name('password.reset');

/* TODO: Figure out why this only works inside of web.php */

/*
// View an episode (episode / screencast player)
Route::get(
    'episodes/{episode:uuid}',
    function (Episode $episode) {
        dd($episode);
    }
)
    //->middleware(CanSeeEpisode::class)
    ->name('episode.play');
*/


Route::get('purchase', CheckoutController::class)
    ->middleware(WithCourseInSession::class)
    ->middlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:5,1')
    ->name('purchase.checkout');
