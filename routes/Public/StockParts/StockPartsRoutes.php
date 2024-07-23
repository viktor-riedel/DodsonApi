<?php

use App\Http\Controllers\StockParts\StockPartsController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock-parts')->group(function () {
   Route::get('/list', [StockPartsController::class, 'list']);
   Route::get('/part/{part}', [StockPartsController::class, 'get']);
   Route::get('/similar/{part}', [StockPartsController::class, 'similar']);

   Route::prefix('search')->group(function () {
        Route::get('/makes', [StockPartsController::class, 'makes']);
        Route::get('/models/{make}', [StockPartsController::class, 'models']);
        Route::get('/years/{make}/{model}', [StockPartsController::class, 'years']);
   });


});
