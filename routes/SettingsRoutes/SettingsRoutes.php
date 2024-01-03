<?php

use App\Http\Controllers\Settings\MarketsController;
use App\Http\Controllers\Settings\YardsController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->middleware('auth:sanctum')->group(function() {
    //yards
    Route::prefix('yards')->group(function() {
        Route::get('/', [YardsController::class, 'index']);
        Route::post('/create-yard', [YardsController::class, 'create']);
        Route::patch('/update-yard/{yard}', [YardsController::class, 'update']);
        Route::delete('/delete-yard/{yard}', [YardsController::class, 'delete']);
    });

    //markets
    Route::prefix('markets')->group(function() {
        Route::get('/', [MarketsController::class, 'list']);
        Route::post('/create-market', [MarketsController::class, 'create']);
        Route::patch('/update-market/{market}', [MarketsController::class, 'update']);
        Route::delete('/delete-market/{market}', [MarketsController::class, 'delete']);
    });
});
