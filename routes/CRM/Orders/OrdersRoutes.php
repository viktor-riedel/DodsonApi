<?php

use App\Http\Controllers\CRM\Orders\OrdersController;
use Illuminate\Support\Facades\Route;

Route::prefix('/orders')->group(function () {
    Route::get('/', [OrdersController::class, 'list'])->name('orders.index');
    Route::get('/{order}', [OrdersController::class, 'view'])->name('orders.view');
});
