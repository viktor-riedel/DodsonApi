<?php

use App\Http\Controllers\UserPanel\Cart\UserCartController;
use App\Http\Controllers\UserPanel\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user-panel')->middleware('auth:sanctum')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'list'])->name('user.dashboard');
    // user cart
    Route::get('/cart', [UserCartController::class, 'list'])->name('user.cart');
});
