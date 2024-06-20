<?php

use App\Http\Controllers\CRM\Orders\OrdersController;
use Illuminate\Support\Facades\Route;

Route::prefix('/orders')->group(function () {
    Route::get('/', [OrdersController::class, 'list']);
    Route::get('/order-statuses', [OrdersController::class, 'statuses']);
    Route::get('/{order}', [OrdersController::class, 'view']);
});
