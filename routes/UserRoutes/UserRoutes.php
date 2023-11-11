<?php

use App\Http\Controllers\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function() {
    Route::get('/', [UsersController::class, 'index'])->name('users.list');
    Route::post('/create', [UsersController::class, 'create'])->name('users.create');
});
