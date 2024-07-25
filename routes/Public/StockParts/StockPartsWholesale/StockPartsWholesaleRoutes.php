<?php

use App\Http\Controllers\StockParts\StockPartWholesaleController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock-parts-wholesale')->group(function () {
    Route::get('/list', [StockPartWholesaleController::class, 'list']);

    Route::prefix('search')->group(function () {
        Route::get('/default-parts', [StockPartWholesaleController::class, 'defaultPartsList']);
        Route::get('/makes', [StockPartWholesaleController::class, 'makes']);
        Route::get('/models/{make}', [StockPartWholesaleController::class, 'models']);
        Route::get('/years/{make}/{model}', [StockPartWholesaleController::class, 'years']);
        Route::get('/engines/{make}/{model}', [StockPartWholesaleController::class, 'engines']);
    });
});
