<?php

use App\Http\Controllers\ReadyCars\ReadyCarsController;
use Illuminate\Support\Facades\Route;

Route::prefix('ready-cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [ReadyCarsController::class, 'list']);
    Route::get('/{make}/models', [ReadyCarsController::class, 'models']);
    Route::get('/{make}/{model}/generations', [ReadyCarsController::class, 'generations']);
});
