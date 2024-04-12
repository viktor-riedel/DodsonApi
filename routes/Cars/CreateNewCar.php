<?php

use App\Http\Controllers\CreateCar\CreateCarController;
use Illuminate\Support\Facades\Route;

Route::prefix('create-car')->middleware('auth:sanctum')->group(function() {
   Route::get('makes', [CreateCarController::class, 'makes']);
   Route::get('/{make}/models', [CreateCarController::class, 'models']);
   Route::get('/{make}/{model}/generations', [CreateCarController::class, 'generations']);
   Route::get('/{make}/{model}/{generation}/modifications', [CreateCarController::class, 'modifications']);
   Route::get('/{make}/{model}/{generation}/{modification}/parts-list', [CreateCarController::class, 'partsList']);
   Route::get('misc-parts-list', [CreateCarController::class, 'miscPartsList']);
   Route::post('/upload-image', [CreateCarController::class, 'uploadPhoto']);
   Route::post('/create-new-car', [CreateCarController::class, 'createNewCar']);
});
