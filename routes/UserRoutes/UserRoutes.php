<?php

use App\Http\Controllers\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [UsersController::class, 'index']);
    Route::post('/create', [UsersController::class, 'create']);
});
