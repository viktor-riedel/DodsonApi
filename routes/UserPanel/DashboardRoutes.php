<?php

use App\Http\Controllers\UserPanel\Cart\UserCartController;
use App\Http\Controllers\UserPanel\Dashboard\DashboardController;
use App\Http\Controllers\UserPanel\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user-panel')->middleware('auth:sanctum')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'list'])->name('user.dashboard');
    // user cart
    Route::get('/cart', [UserCartController::class, 'list'])->name('user.cart');
    //wish list
    Route::get('/wish-list', [UserCartController::class, 'wishList'])->name('user.wish-list');
    //order
    Route::prefix('/orders')->group(function() {
       Route::get('/', [OrderController::class, 'list'])->name('user.orders');
       Route::post('/create', [OrderController::class, 'create'])->name('user.orders.store');
    });
});
