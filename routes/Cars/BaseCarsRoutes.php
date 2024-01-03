<?php

use App\Http\Controllers\Cars\BaseCarsController;
use Illuminate\Support\Facades\Route;

Route::prefix('base-cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [BaseCarsController::class, 'list']);
    Route::prefix('/add-new')->group(function() {
       Route::get('/makes', [BaseCarsController::class, 'makes']);
        Route::get('/models/{make}', [BaseCarsController::class, 'models']);
    });
});
