<?php

use App\Http\Controllers\ReadyCars\ReadyCarsController;
use Illuminate\Support\Facades\Route;

Route::prefix('ready-cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [ReadyCarsController::class, 'list']);
    Route::get('/{make}/models', [ReadyCarsController::class, 'models']);
    Route::get('/{make}/{model}/generations', [ReadyCarsController::class, 'generations']);
    Route::get('/{make}/{model}/{generation}/modifications', [ReadyCarsController::class, 'modifications']);
    Route::get('/{make}/{model}/parts', [ReadyCarsController::class, 'partsList']);
});
