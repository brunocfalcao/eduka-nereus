<?php

use Eduka\Cube\Models\Chapter;
use Eduka\Cube\Models\Coupon;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Order;
use Eduka\Cube\Models\Series;
use Eduka\Cube\Models\User;
use Eduka\Cube\Models\Video;
use Eduka\Nereus\Http\Controllers\NewsletterController;
use Eduka\Nereus\Http\Controllers\Prelaunched\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', [Welcome::class, 'index'])
    ->name('welcome.default');

Route::post('/subscribe-to-newsletter', [NewsletterController::class, 'subscribeToNewsletter'])
    ->name('subscribe.newsletter');

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
