<?php

use App\Http\Controllers\CRM\Orders\OrdersController;
use Illuminate\Support\Facades\Route;

Route::prefix('/orders')->group(function () {
    Route::get('/', [OrdersController::class, 'list']);
    Route::get('/order-users', [OrdersController::class, 'users']);
    Route::get('/export-orders', [OrdersController::class, 'export']);
    Route::get('/order-statuses', [OrdersController::class, 'statuses']);
    Route::get('/{order}', [OrdersController::class, 'view']);
    Route::patch('/{order}', [OrdersController::class, 'update']);
});
