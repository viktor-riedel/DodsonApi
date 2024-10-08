<?php

use App\Http\Controllers\Public\LeftoversController;
use App\Http\Controllers\Public\Nomenclature\CardsController;
use App\Http\Controllers\Public\Nomenclature\MakesController;
use App\Http\Controllers\Public\Nomenclature\ModelsController;
use App\Http\Controllers\Public\Nomenclature\ModificationsController;
use App\Http\Controllers\Public\Nomenclature\PartsController;
use Illuminate\Support\Facades\Route;

Route::namespace('Public')->middleware(['auth:sanctum', 'is_web_user'])->group(function() {
    Route::prefix('nomenclature')->group(function() {
        Route::get('/defaults', [PartsController::class, 'defaults']);
        Route::get('/makes', [MakesController::class, 'list']);
        Route::get('/makes/{make}/models', [ModelsController::class, 'list']);
        Route::get('/makes/{make}/models/{model}/parts', [PartsController::class, 'list']);
        Route::get('/makes/{make}/models/{model}/{generation}/modifications', [ModificationsController::class, 'list']);
        Route::prefix('cards')->group(function() {
            Route::get('/{id}', [CardsController::class, 'index']);
            Route::post('/{id}/update', [CardsController::class, 'update']);
        });
    });
    Route::prefix('stats')->group(function() {
        Route::post('/update', [LeftoversController::class, 'update']);
    });
});
