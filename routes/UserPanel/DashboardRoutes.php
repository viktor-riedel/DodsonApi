<?php

use App\Http\Controllers\UserPanel\Cart\UserCartController;
use App\Http\Controllers\UserPanel\Dashboard\DashboardController;
use App\Http\Controllers\UserPanel\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user-panel')->middleware('auth:sanctum')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'list']);
    // user cart
    Route::get('/cart', [UserCartController::class, 'list']);
    //wish list
    Route::get('/wish-list', [UserCartController::class, 'wishList']);
    //order
    Route::prefix('/orders')->group(function() {
       Route::get('/', [OrderController::class, 'list']);
        Route::get('/{order}', [OrderController::class, 'order']);
       Route::post('/create', [OrderController::class, 'create']);
    });
});
