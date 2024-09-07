<?php

use App\Http\Controllers\Balances\UserBalancesController;

Route::prefix('/user-balances')->group(function () {
    Route::get('/', [UserBalancesController::class, 'list']);
    Route::get('/users', [UserBalancesController::class, 'listUsers']);
    Route::get('/balance/{user}', [UserBalancesController::class, 'getUserBalance']);
    Route::get('/user/{user}', [UserBalancesController::class, 'getBalancedUser']);
});
