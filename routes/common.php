<?php

use Illuminate\Support\Facades\Route;

Route::get('/dev/resources/{resource}/{id?}', function (string $resource, string $id = null) {

    abort_unless(config('app.env') === 'local', 404);

    $model = match ($resource) {
        'user', 'users' => User::query(),
        'chapter', 'chapters' => Chapter::query(),
        'course', 'courses' => Course::query(),
        'order', 'orders' => Order::query(),
        'series', 'series' => Series::query(),
        'video', 'videos' => Video::query(),
        'coupon', 'coupons' => Coupon::query(),

        default => null,
    };

    if (! $model) {
        return [
            'no resource',
        ];
    }

    if ($id) {
        $model = $model->where('id', $id);
    }

    return $model->orderBy('id')->get();
});
