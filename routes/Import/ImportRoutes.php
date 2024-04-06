<?php

use App\Http\Controllers\Import\CarController;
use Illuminate\Support\Facades\Route;

Route::prefix('import')->middleware('auth:sanctum')->group(function() {
   Route::prefix('caparts-cars')->group(function() {
       Route::get('/import-cars', [CarController::class, 'importResources']);
       Route::post('/import-car', [CarController::class, 'importEntity']);
   });
});
