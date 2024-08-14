<?php

use Illuminate\Support\Facades\Route;
use Ogrre\ApiAuth\Controllers\ApiAuthController;

Route::prefix(config('api-auth.route_prefix', 'api/auth'))->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/forgot-password', [ApiAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [ApiAuthController::class, 'resetPassword']);

    Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
});
