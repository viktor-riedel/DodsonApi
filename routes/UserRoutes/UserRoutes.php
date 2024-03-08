<?php

use App\Http\Controllers\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [UsersController::class, 'index']);
    Route::get('/edit/{user}', [UsersController::class, 'edit']);
    Route::post('/create', [UsersController::class, 'create']);
});

Route::prefix('roles')->middleware('auth:sanctum')->group(function() {

});

Route::prefix('permissions')->middleware('auth:sanctum')->group(function() {

});
