<?php

use App\Http\Controllers\CreateCar\CreateCarController;
use App\Http\Controllers\CreateCar\CreateCarFromCatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('create-car')->middleware('auth:sanctum')->group(function() {
   Route::prefix('nomenclature')->group(function() {
       Route::get('makes', [CreateCarController::class, 'makes']);
       Route::get('/{make}/models', [CreateCarController::class, 'models']);
       Route::get('/{make}/{model}/generations', [CreateCarController::class, 'generations']);
       Route::get('/{make}/{model}/{generation}/modifications', [CreateCarController::class, 'modifications']);
       Route::get('/{make}/{model}/{generation}/{modification}/parts-list', [CreateCarController::class, 'partsList']);
       Route::get('misc-parts-list', [CreateCarController::class, 'miscPartsList']);
       Route::post('/upload-image', [CreateCarController::class, 'uploadPhoto']);
       Route::post('/create-new-car', [CreateCarController::class, 'createNewCar']);
   });
   Route::prefix('catalog')->group(function() {
       Route::get('/makes', [CreateCarFromCatalogController::class, 'makes']);
       Route::get('/{make}/models', [CreateCarFromCatalogController::class, 'models']);
       Route::get('/{model}/generations', [CreateCarFromCatalogController::class, 'generations']);
       Route::get('/{model}/{generation}modifications', [CreateCarFromCatalogController::class, 'modificationsByGeneration']);
       Route::post('/create-new-car', [CreateCarFromCatalogController::class, 'createNewCar']);
   });
});
