<?php

use App\Http\Controllers\CreateCar\CreateCarController;
use Illuminate\Support\Facades\Route;

Route::prefix('create-car')->group(function() {
   Route::get('makes', [CreateCarController::class, 'makes']);
   Route::get('{makes}/models', [CreateCarController::class, 'models']);
   Route::get('{make}/{model}/generations', [CreateCarController::class, 'generations']);
   Route::get('{make}/{model}/{generation}/modifications', [CreateCarController::class, 'modifications']);
});
