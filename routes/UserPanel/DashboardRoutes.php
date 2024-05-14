<?php

use App\Http\Controllers\UserPanel\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user-panel')->middleware('auth:sanctum')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'list'])->name('user.dashboard');
});
