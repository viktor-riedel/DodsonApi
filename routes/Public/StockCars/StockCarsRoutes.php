<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\StockCars\StockCarsController;
use App\Http\Controllers\UserPanel\Order\OrderController;
use App\Http\Controllers\WishList\WishListController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock-cars')->group(function () {
    Route::get('/', [StockCarsController::class, 'list']);
    Route::get('/car/{car}', [StockCarsController::class, 'view']);
    Route::get('/makes', [StockCarsController::class, 'makes']);
    Route::get('/{make}/models', [StockCarsController::class, 'models']);
    Route::get('/{make}/{model}/generations', [StockCarsController::class, 'generations']);
    Route::get('/{make}/{model}/years', [StockCarsController::class, 'years']);
    Route::get('/{make}/{model}/{generation}/modifications', [StockCarsController::class, 'modifications']);

    Route::middleware(['auth:sanctum', 'is_web_user'])->group(function() {
        Route::prefix('/wish-list')->group(function() {
            Route::put('/add/{car}', [WishListController::class, 'addWishList']);
            Route::get('/', [WishListController::class, 'list']);
        });
        Route::prefix('/cart')->group(function() {
            Route::get('/items', [CartController::class, 'items']);
            Route::delete('/items/{car}', [CartController::class, 'delete']);
            Route::put('/add/{car}', [CartController::class, 'add']);
        });
        Route::prefix('/orders')->group(function() {
           Route::post('/{car}/make-car-order', [OrderController::class, 'createCarOrder']);
        });
    });
});
