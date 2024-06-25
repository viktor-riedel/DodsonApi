<?php

use App\Http\Controllers\AllCars\AllCarsController;
use App\Http\Controllers\EditCar\EditCarController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->middleware('auth:sanctum')->group(function() {
    Route::get('/list', [AllCarsController::class, 'list']);
    Route::get('/currencies-list', [AllCarsController::class, 'currencyList']);
    Route::get('/status-list', [AllCarsController::class, 'statusList']);
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
        Route::get('/sync-car', [EditCarController::class, 'syncCar']);
        Route::get('/generate-dismantling-badges', [EditCarController::class, 'generateDismantlingBadges']);
        Route::get('/generate-dismantling-document', [EditCarController::class, 'generateDismantlingDocument']);
        Route::delete('/delete-part/{card}', [EditCarController::class, 'deletePart']);
        Route::post('/upload-part-photo/{card}', [EditCarController::class, 'uploadPartPhoto']);
        Route::delete('/delete-part-photo/{card}/{photo}', [EditCarController::class, 'deletePartPhoto']);
        Route::patch('/update-attributes/{card}', [EditCarController::class, 'updateAttributes']);
        Route::patch('/update-card-price/{card}', [EditCarController::class, 'updatePriceCard']);
        Route::put('/add-misc-parts', [EditCarController::class, 'addMiscParts']);
        Route::put('/add-parts-from-list', [EditCarController::class, 'addListParts']);
        Route::put('/add-parts-from-mod-list', [EditCarController::class, 'addModListParts']);
        Route::put('/add-parts-from-selling-list', [EditCarController::class, 'addSellingListParts']);
        Route::get('/export-parts-list', [EditCarController::class, 'exportPartsListToExcel']);

        Route::prefix('/update-parts-list')->group(function() {
            Route::patch('/ic-number/{card}', [EditCarController::class, 'updateICNumber']);
            Route::patch('/price-currency/{card}', [EditCarController::class, 'updatePriceCurrency']);
            Route::patch('/buying-price/{card}', [EditCarController::class, 'updateBuyingPrice']);
            Route::patch('/selling-price/{card}', [EditCarController::class, 'updateSellingPrice']);
            Route::patch('/comment/{card}', [EditCarController::class, 'updateComment']);
            Route::patch('/ic-description/{card}', [EditCarController::class, 'updateIcDescription']);
            Route::patch('/set-parts-price', [EditCarController::class, 'setPartsPrice']);
            Route::patch('/set-part-client/{card}', [EditCarController::class, 'setClient']);
        });

        Route::prefix('/links')->group(function() {
           Route::get('/', [EditCarController::class, 'linksList']);
           Route::post('/add-link', [EditCarController::class, 'addLink']);
           Route::delete('/{link}/delete', [EditCarController::class, 'deleteLink']);
        });
    });
});
