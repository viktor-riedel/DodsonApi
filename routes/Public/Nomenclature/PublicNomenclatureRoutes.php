<?php

use App\Http\Controllers\Public\Nomenclature\MakesController;
use App\Http\Controllers\Public\Nomenclature\ModelsController;
use Illuminate\Support\Facades\Route;

Route::namespace('Public')->middleware('auth:sanctum')->group(function() {
    Route::prefix('nomenclature')->group(function() {
        Route::get('/makes', [MakesController::class, 'list']);
        Route::get('/makes/{make}/models', [ModelsController::class, 'list']);
    });
});
