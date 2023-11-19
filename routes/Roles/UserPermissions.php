<?php

use App\Http\Controllers\Permissions\UserPermissionsController;
use Illuminate\Support\Facades\Route;

Route::prefix('user-permissions')->group(function() {
    Route::get('/roles', [UserPermissionsController::class, 'getUserRoles']);
});
