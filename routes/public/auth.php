<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function() {
   Route::post('/login', [AuthController::class, 'login']);
   Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});
