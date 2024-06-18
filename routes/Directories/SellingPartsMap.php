<?php

use App\Http\Controllers\SellingPartsMap\SellingPartsMapController;
use Illuminate\Support\Facades\Route;

Route::prefix('selling-parts-map')->group(function() {
    Route::get('/list', [SellingPartsMapController::class, 'list']);
    Route::get('/parts-list', [SellingPartsMapController::class, 'partsList']);
    Route::prefix('/{item}')->group(function() {
        Route::post('/add-part-list', [SellingPartsMapController::class, 'addPartToGroup']);
        Route::post('/add-parts-list', [SellingPartsMapController::class, 'addPartsToGroup']);
        Route::delete('/delete-part', [SellingPartsMapController::class, 'deletePart']);
        Route::patch('/update-part-price', [SellingPartsMapController::class, 'updatePartPrice']);
    });
});
