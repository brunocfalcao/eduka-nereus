<?php

use Illuminate\Http\Request;
use Eduka\Nereus\Http\Controllers\Launched;
use Eduka\Payments\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [Launched::class, 'welcome'])
    ->middleware('guest')
    ->middlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:10,1')
    ->name('launched.welcome');

Route::get('thanks-for-buying', RedirectController::class)
    ->middlewareWhen(function (Request $request) {
        return app()->environment() != 'local';
    }, 'throttle:2,1')
    ->name('purchase.callback');
