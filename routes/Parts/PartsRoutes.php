<?php

use App\Http\Controllers\Parts\PartsController;
use Illuminate\Support\Facades\Route;

Route::prefix('parts')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [PartsController::class, 'list']);
    Route::prefix('/search')->group(function() {
        Route::get('/makes', [PartsController::class, 'makes']);
        Route::get('/models/{make}', [PartsController::class, 'models']);
    });

    Route::prefix('part')->group(function() {
        Route::get('/{part}', [PartsController::class, 'get']);
        Route::delete('/delete/{part}', [PartsController::class, 'delete']);
    });

    Route::prefix('import')->group(function() {
        Route::post('/from-pinnacle', [PartsController::class, 'importFromPinnacle']);
        Route::post('/from-one-c', [PartsController::class, 'importFromOneC']);
    });
});
