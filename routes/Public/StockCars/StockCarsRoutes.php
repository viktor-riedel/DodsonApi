<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\StockCars\StockCarsController;
use App\Http\Controllers\WishList\WishListController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock-cars')->group(function () {
    Route::get('/', [StockCarsController::class, 'list'])->name('stock-cars.list');
    Route::get('/car/{car}', [StockCarsController::class, 'view'])->name('stock-cars.car');
    Route::get('/makes', [StockCarsController::class, 'makes'])->name('stock-cars.makes');
    Route::get('/{make}/models', [StockCarsController::class, 'models'])->name('stock-cars.models');
    Route::get('/{make}/{model}/generations', [StockCarsController::class, 'generations'])->name('stock-cars.generations');
    Route::get('/{make}/{model}/years', [StockCarsController::class, 'years'])->name('stock-cars.years');
    Route::get('/{make}/{model}/{generation}/modifications', [StockCarsController::class, 'modifications'])->name('stock-cars.modifications');

    Route::middleware('auth:sanctum')->group(function() {
        Route::prefix('/wish-list')->group(function() {
            Route::put('/add/{car}', [WishListController::class, 'addWishList'])->name('user.wish-list.add');
            Route::get('/', [WishListController::class, 'list'])->name('user.wish-list');
        });
        Route::prefix('/cart')->group(function() {
            Route::get('/items', [CartController::class, 'items'])->name('cart.items');
            Route::delete('/items/{car}', [CartController::class, 'delete'])->name('cart.items.delete');
            Route::put('/add/{car}', [CartController::class, 'add'])->name('user.cart.add');
        });
    });
});
