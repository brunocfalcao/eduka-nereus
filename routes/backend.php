<?php

use Illuminate\Support\Facades\Route;

Route::view('/password/reset/{token}?email={email}', 'backend::auth.password.reset')
    ->name('password.reset');

Route::get('/', function () {
    return 'hello world';
});
