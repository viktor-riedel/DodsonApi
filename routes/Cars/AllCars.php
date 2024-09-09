<?php

use App\Http\Controllers\AllCars\AllCarsController;
use App\Http\Controllers\EditCar\EditCarController;
use App\Http\Controllers\Pricing\PricingController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->middleware(['auth:sanctum', 'is_web_user'])->group(function() {
    Route::get('/list', [AllCarsController::class, 'list']);
    Route::get('/currencies-list', [AllCarsController::class, 'currencyList']);
    Route::get('/status-list', [AllCarsController::class, 'statusList']);
    Route::get('/users-list', [AllCarsController::class, 'usersList']);
    Route::get('/agents-list', [AllCarsController::class, 'agentsList']);
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
        Route::post('/generate-dismantling-badges', [EditCarController::class, 'generateDismantlingBadges']);
        Route::get('/generate-dismantling-document', [EditCarController::class, 'generateDismantlingDocument']);
        Route::delete('/delete-part/{card}', [EditCarController::class, 'deletePart']);
        Route::patch('/delete-parts', [EditCarController::class, 'deleteParts']);
        Route::post('/upload-part-photo/{card}', [EditCarController::class, 'uploadPartPhoto']);
        Route::delete('/delete-part-photo/{card}/{photo}', [EditCarController::class, 'deletePartPhoto']);
        Route::patch('/update-attributes/{card}', [EditCarController::class, 'updateAttributes']);
        Route::patch('/update-card-price/{card}', [EditCarController::class, 'updatePriceCard']);
        Route::patch('/update-original-card-price/{card}', [EditCarController::class, 'updateOriginalPriceCard']);
        Route::put('/add-misc-parts', [EditCarController::class, 'addMiscParts']);
        Route::put('/add-parts-from-list', [EditCarController::class, 'addListParts']);
        Route::put('/add-parts-from-mod-list', [EditCarController::class, 'addModListParts']);
        Route::put('/add-parts-from-selling-list', [EditCarController::class, 'addSellingListParts']);
        Route::get('/export-parts-list', [EditCarController::class, 'exportPartsListToExcel']);
        Route::patch('/update-modification', [EditCarController::class, 'updateModification']);
        Route::patch('/set-parts-user/{user}', [EditCarController::class, 'setPartsUser']);

        Route::prefix('/update-parts-list')->group(function() {
            Route::patch('/ic-number/{card}', [EditCarController::class, 'updateICNumber']);
            Route::patch('/price-currency/{card}', [EditCarController::class, 'updatePriceCurrency']);
            Route::patch('/buying-price/{card}', [EditCarController::class, 'updateBuyingPrice']);
            Route::patch('/selling-price/{card}', [EditCarController::class, 'updateSellingPrice']);
            Route::patch('/update-prices', [EditCarController::class, 'updateSellingBuyingPrices']);
            Route::patch('/set-default-price-category', [EditCarController::class, 'setDefaultPriceCategory']);
            Route::prefix('/comment')->group(function() {
                Route::patch('/{card}', [EditCarController::class, 'updateComment']);
                Route::delete('/{card}', [EditCarController::class, 'deleteComments']);
            });
            Route::patch('/ic-description/{card}', [EditCarController::class, 'updateIcDescription']);
            Route::patch('/set-parts-price', [EditCarController::class, 'setPartsPrice']);
            Route::patch('/set-part-client/{card}', [EditCarController::class, 'setClient']);
            Route::patch('/set-parts-client', [EditCarController::class, 'setPartsClient']);
        });

        Route::prefix('/pricing')->group(function() {
           Route::get('/list', [PricingController::class, 'list']);
           Route::patch('/update-selling-prices', [PricingController::class, 'updateSellingPrices']);
        });

        Route::prefix('/links')->group(function() {
           Route::get('/', [EditCarController::class, 'linksList']);
           Route::post('/add-link', [EditCarController::class, 'addLink']);
           Route::delete('/{link}/delete', [EditCarController::class, 'deleteLink']);
        });

        Route::prefix('/parts-comments')->group(function() {
            Route::get('/list', [EditCarController::class, 'partsCommentsList']);
            Route::post('/add-comment', [EditCarController::class, 'addComment']);
        });
    });
});
