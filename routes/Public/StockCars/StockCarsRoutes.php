<?php

use App\Http\Controllers\StockCars\StockCarsController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock-cars')->group(function () {
    Route::get('/', [StockCarsController::class, 'list'])->name('stock-cars.list');
    Route::get('/makes', [StockCarsController::class, 'makes'])->name('stock-cars.makes');
    Route::get('/{make}/models', [StockCarsController::class, 'models'])->name('stock-cars.models');
    Route::get('/{make}/{model}/generations', [StockCarsController::class, 'generations'])->name('stock-cars.generations');
    Route::get('/{make}/{model}/{generation}/modifications', [StockCarsController::class, 'modifications'])->name('stock-cars.modifications');

    Route::middleware('auth:sanctum')->group(function() {
        Route::prefix('/wish-list')->group(function() {
            ROute::get('/wished/{car}', [StockCarsController::class, 'carWished'])->name('user.wish-list.car-wished');
            Route::put('/add/{car}', [StockCarsController::class, 'addWishList'])->name('user.wish-list.add');
        });
    });
});
