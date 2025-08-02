<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/test-reverb-env', function () {
    // Acceder al array de apps definidas en config/reverb.php
    $reverbApps = config('reverb.apps.apps');

    // Acceder a la primera aplicación en ese array (índice 0)
    $firstApp = $reverbApps[0] ?? []; // Usar ?? [] para evitar error si el array está vacío

    return [
        'REVERB_APP_ID_FROM_ENV' => env('REVERB_APP_ID'),
        'REVERB_APP_ID_FROM_CONFIG' => $firstApp['app_id'] ?? null, // Acceder al 'app_id'
        'REVERB_APP_KEY_FROM_ENV' => env('REVERB_APP_KEY'),
        'REVERB_APP_KEY_FROM_CONFIG' => $firstApp['key'] ?? null,
    ];
});