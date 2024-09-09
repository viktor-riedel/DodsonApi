<?php

use App\Http\Controllers\Cars\BaseCarsController;
use Illuminate\Support\Facades\Route;

Route::prefix('base-cars')->middleware(['auth:sanctum', 'is_web_user'])->group(function() {
    Route::get('/', [BaseCarsController::class, 'list']);
    Route::get('/find/{baseCar}', [BaseCarsController::class, 'find']);
    Route::post('/add-new', [BaseCarsController::class, 'create']);
    Route::delete('/delete/{baseCar}', [BaseCarsController::class, 'delete']);
    Route::patch('/update/{baseCar}', [BaseCarsController::class, 'update']);

    //data
    Route::prefix('/add-new')->group(function() {
        Route::get('/makes', [BaseCarsController::class, 'makes']);
        Route::get('/models/{make}', [BaseCarsController::class, 'models']);
        Route::get('/headers/{make}/{model}/{generation}', [BaseCarsController::class, 'getHeaders']);
    });
});
