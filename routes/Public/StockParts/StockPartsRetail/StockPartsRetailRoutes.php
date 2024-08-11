<?php

use App\Http\Controllers\StockParts\StockPartsRetailController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock-parts-retail')->group(function () {
    Route::get('/list', [StockPartsRetailController::class, 'list']);
    Route::get('/part/{part}', [StockPartsRetailController::class, 'get']);
    Route::get('/similar/{part}', [StockPartsRetailController::class, 'similar']);

    Route::prefix('search')->group(function () {
        Route::get('/makes', [StockPartsRetailController::class, 'makes']);
        Route::get('/models/{make}', [StockPartsRetailController::class, 'models']);
        Route::get('/years/{make}/{model}', [StockPartsRetailController::class, 'years']);
    });
});
