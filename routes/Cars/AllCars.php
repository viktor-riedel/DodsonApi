<?php

use App\Http\Controllers\AllCars\AllCarsController;
use App\Http\Controllers\EditCar\EditCarController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/list', [AllCarsController::class, 'list']);
    Route::get('/makes', [AllCarsController::class, 'makes']);
    Route::get('/{make}/models', [AllCarsController::class, 'models']);
    Route::get('/{make}/{model}/generations', [AllCarsController::class, 'generations']);
    Route::prefix('/{car}')->group(function() {
        Route::get('/car-default-parts-list', [EditCarController::class, 'parts']);
        Route::get('/edit', [EditCarController::class, 'edit']);
        Route::delete('/delete', [EditCarController::class, 'delete']);
        Route::post('/upload-car-photo', [EditCarController::class, 'uploadCarPhoto']);
        Route::delete('/delete-car-photo/{photo}', [EditCarController::class, 'deleteCarPhoto']);
        Route::patch('/update-car', [EditCarController::class, 'updateCar']);
        Route::patch('/update-car-status', [EditCarController::class, 'updateCarStatus']);
        Route::delete('/delete-part/{card}', [EditCarController::class, 'deletePart']);
        Route::post('/upload-part-photo/{card}', [EditCarController::class, 'uploadPartPhoto']);
        Route::patch('/update-attributes/{card}', [EditCarController::class, 'updateAttributes']);
        Route::put('/add-misc-parts', [EditCarController::class, 'addMiscParts']);
    });
});
