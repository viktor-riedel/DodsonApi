<?php

use App\Http\Controllers\Cars\CarController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/import-unsold-cars', [CarController::class, 'importUnsoldCards']);
});
