<?php

use App\Http\Controllers\Balances\UserBalancesController;

Route::prefix('/user-balances')->group(function () {
    Route::get('/', [UserBalancesController::class, 'list']);
    Route::get('/users', [UserBalancesController::class, 'listUsers']);
});
