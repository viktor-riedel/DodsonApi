<?php

use App\Http\Controllers\StockParts\StockPartWholesaleController;
use App\Http\Controllers\UserPanel\Order\WholesalePartsOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock-parts-wholesale')->group(function () {
    Route::get('/list', [StockPartWholesaleController::class, 'list']);
    Route::get('/part/{part}', [StockPartWholesaleController::class, 'get']);

    Route::prefix('search')->group(function () {
        Route::get('/default-parts', [StockPartWholesaleController::class, 'defaultPartsList']);
        Route::get('/makes', [StockPartWholesaleController::class, 'makes']);
        Route::get('/models/{make}', [StockPartWholesaleController::class, 'models']);
        Route::get('/years/{make}/{model}', [StockPartWholesaleController::class, 'years']);
        Route::get('/generations/{make}/{model}', [StockPartWholesaleController::class, 'generations']);
        Route::get('/engines/{make}/{model}/{year}', [StockPartWholesaleController::class, 'engines']);
    });

    Route::prefix('/order')->middleware(['auth:sanctum', 'is_web_user'])->group(function () {
        Route::get('/cart', [WholesalePartsOrderController::class, 'cart']);
        Route::put('/add-to-cart', [WholesalePartsOrderController::class, 'addToCart']);
        Route::patch('/update-cart', [WholesalePartsOrderController::class, 'updateCart']);
        Route::post('/make-parts-order', [WholesalePartsOrderController::class, 'makePartsOrder']);
    });
});
