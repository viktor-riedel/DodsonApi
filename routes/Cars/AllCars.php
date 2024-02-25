<?php

use App\Http\Controllers\AllCars\AllCarsController;
use App\Http\Controllers\EditCar\EditCarController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/list', [AllCarsController::class, 'list']);
    Route::get('/edit/{car}', [EditCarController::class, 'edit']);
});
