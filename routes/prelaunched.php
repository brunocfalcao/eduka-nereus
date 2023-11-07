<?php

use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Order;
use Eduka\Nereus\Http\Controllers\NewsletterController;
use Eduka\Nereus\Http\Controllers\Prelaunched\Welcome;
use Eduka\Payments\Actions\LemonSqueezyWebhookPayloadExtractor;
use Illuminate\Support\Facades\Route;

Route::get('/', [Welcome::class, 'index'])
    ->name('welcome.default');

Route::post('/subscribe-to-newsletter', [NewsletterController::class, 'subscribeToNewsletter'])
    ->name('subscribe.newsletter');

Route::get('/x', function () {

    $orders = Order::all();

    foreach($orders as $order) {
        // $json = json_decode($order->response_body, true);

        // $extracted = (new LemonSqueezyWebhookPayloadExtractor)->extract($json);
        // $order->update($extracted);
        $order->update([
            'created_at' => now()->subDays(random_int(0,90)),
        ]);
    }
});
