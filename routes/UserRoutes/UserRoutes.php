<?php

use App\Http\Controllers\Users\RolesController;
use App\Http\Controllers\Users\RolesPermissionsController;
use App\Http\Controllers\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [UsersController::class, 'list']);
    Route::get('/countries-list', [UsersController::class, 'countriesList']);
    Route::prefix('edit/{user}')->group(function() {
        Route::get('/', [UsersController::class, 'edit']);
        Route::patch('/block', [UsersController::class, 'blockUser']);
        Route::put('/update', [UsersController::class, 'update']);
    });
    Route::post('/create', [UsersController::class, 'create']);
});

Route::prefix('roles')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [RolesController::class, 'list']);
    Route::post('/create', [RolesController::class, 'create']);
});

Route::prefix('permissions')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [RolesPermissionsController::class, 'list']);
    Route::put('/assign-permissions', [RolesPermissionsController::class, 'assign']);
});
