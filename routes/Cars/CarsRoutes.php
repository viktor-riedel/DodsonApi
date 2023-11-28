<?php

use App\Http\Controllers\Cars\CarController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->group(function() {
    Route::get('/import-unsold-cars', [CarController::class, 'importUnsoldCards']);
});
