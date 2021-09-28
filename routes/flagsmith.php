<?php

use Clearlyip\LaravelFlagsmith\Http\Controllers\Webhooks;
use Illuminate\Support\Facades\Route;

if (!empty(config('flagsmith.webhooks.feature.route'))) {
    Route::middleware(
        config('flagsmith.webhooks.feature.middleware', [])
    )->post(config('flagsmith.webhooks.feature.route'), [
        Webhooks::class,
        'feature',
    ]);
}
