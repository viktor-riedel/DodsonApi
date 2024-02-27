<?php

use App\Http\Controllers\AllCars\AllCarsController;
use App\Http\Controllers\EditCar\EditCarController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/list', [AllCarsController::class, 'list']);
    Route::prefix('/{car}')->group(function() {
        Route::get('/edit', [EditCarController::class, 'edit']);
        Route::post('/upload-car-photo', [EditCarController::class, 'uploadCarPhoto']);
        Route::delete('/delete-car-photo/{photo}', [EditCarController::class, 'deleteCarPhoto']);
        Route::patch('/update-car', [EditCarController::class, 'updateCar']);
    });
});
