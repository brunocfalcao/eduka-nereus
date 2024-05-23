<?php

use Eduka\Nereus\Http\Controllers\Prelaunched;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [Prelaunched::class, 'welcome'])
    ->name('prelaunched.welcome');

Route::post('/', [Prelaunched::class, 'subscribe'])
    ->middleware(ProtectAgainstSpam::class)
    ->name('prelaunched.subscribe');

// Dump routes for debugging with formatted output
Route::get('/route/list', function () {
    Artisan::call('route:list');
    $output = Artisan::output();

    // Format output as HTML table
    $lines = explode("\n", trim($output));
    $htmlOutput = '<table border="1" cellspacing="0" cellpadding="5">';
    foreach ($lines as $line) {
        $htmlOutput .= '<tr>';
        foreach (explode(' ', preg_replace('/\s+/', ' ', $line)) as $cell) {
            $htmlOutput .= '<td>' . htmlentities($cell) . '</td>';
        }
        $htmlOutput .= '</tr>';
    }
    $htmlOutput .= '</table>';

    return $htmlOutput;
})->name('route.list');
