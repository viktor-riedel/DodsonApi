<?php

use App\Http\Controllers\StockParts\StockPartsRetailController;
use App\Http\Controllers\UserPanel\Order\RetailPartOrderController;
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

    Route::prefix('/order')->middleware(['auth:sanctum', 'is_web_user'])->group(function () {
        Route::get('/cart', [RetailPartOrderController::class, 'cart']);
        Route::put('/add-to-cart', [RetailPartOrderController::class, 'addToCart']);
        Route::patch('/update-cart', [RetailPartOrderController::class, 'updateCart']);
        Route::post('/make-parts-order', [RetailPartOrderController::class, 'makePartsOrder']);
    });
});
