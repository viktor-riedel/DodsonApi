<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/crm')->group(
    base_path('/routes/CRM/Leads/LeadsRoutes.php'),
);
