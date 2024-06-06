<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function() {
   Route::post('/login', [AuthController::class, 'login'])
       ->middleware('throttle:api');
   Route::post('/forgot', [AuthController::class, 'forgetPassword'])
       ->middleware('throttle:api');
   Route::post('/restore', [AuthController::class, 'restorePassword'])
        ->middleware('throttle:api');
   Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

   Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
