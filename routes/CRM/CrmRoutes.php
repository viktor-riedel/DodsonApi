<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'is_web_user'])->group(function () {
    Route::prefix('/crm')->group(
        base_path('/routes/CRM/Leads/LeadsRoutes.php'),
    );

    Route::prefix('/crm')->group(
        base_path('/routes/CRM/Orders/OrdersRoutes.php'),
    );

    Route::prefix('/crm')->group(
        base_path('/routes/CRM/Bot/BotRoutes.php'),
    );
});
