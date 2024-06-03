<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/crm')->group(
    base_path('/routes/CRM/Leads/LeadsRoutes.php'),
);

Route::prefix('/crm')->group(
    base_path('/routes/CRM/Orders/OrdersRoutes.php'),
);
