<?php

use App\Http\Controllers\Parts\CreateWholesalePart\CreateWholesalePartController;
use App\Http\Controllers\Parts\ListWholesalePartsController;
use App\Http\Controllers\Parts\PartsController;
use Illuminate\Support\Facades\Route;

Route::prefix('parts')->middleware(['auth:sanctum', 'is_web_user'])->group(function () {
    Route::get('/list', [PartsController::class, 'list']);
    Route::prefix('/search')->group(function() {
        Route::get('/part-names', [PartsController::class, 'partNames']);
        Route::get('/part-groups', [PartsController::class, 'partGroups']);
        Route::get('/makes', [PartsController::class, 'makes']);
        Route::get('/models', [PartsController::class, 'models']);
        Route::get('/years', [PartsController::class, 'years']);
    });

    Route::prefix('/part/{part}')->group(function() {
        Route::get('/', [PartsController::class, 'get']);
        Route::delete('/delete', [PartsController::class, 'delete']);
        Route::patch('/update', [PartsController::class, 'update']);
        Route::post('/upload-photo', [PartsController::class, 'uploadPhoto']);
        Route::delete('/delete-photo/{photo}', [PartsController::class, 'deletePhoto']);
    });

    Route::prefix('/trade-me/{part}')->group(function() {
        Route::get('/listing', [PartsController::class, 'tradeMeListing']);
        Route::post('/create', [PartsController::class, 'createTradeMeListing']);
        Route::patch('/update', [PartsController::class, 'updateTradeMeListing']);
        Route::delete('/delete', [PartsController::class, 'deleteTradeMeListing']);
    });

    Route::prefix('import')->group(function() {
        Route::post('/from-pinnacle', [PartsController::class, 'importFromPinnacle']);
        Route::post('/from-one-c', [PartsController::class, 'importFromOneC']);
    });

    //create parts
    Route::prefix('wholesale')->group(function() {
        Route::get('/default-selling-parts', [ListWholesalePartsController::class, 'defaultSellingParts']);
        Route::get('contr-agents', [CreateWholesalePartController::class, 'getAgents']);
        Route::prefix('create')->group(function() {
            Route::get('makes', [CreateWholesalePartController::class, 'getMakes']);
            Route::get('models/{make}', [CreateWholesalePartController::class, 'getModels']);
            Route::get('generations/{make}/{model}', [CreateWholesalePartController::class, 'getGenerations']);
            Route::get('modifications/{make}/{model}/{generation}', [CreateWholesalePartController::class, 'getModifications']);
            Route::get('parts/{modification}', [CreateWholesalePartController::class, 'getParts']);
            Route::post('create-parts', [CreateWholesalePartController::class, 'createParts']);
        });

        Route::prefix('images')->group(function() {
            Route::post('upload-part-image/{part}', [CreateWholesalePartController::class, 'uploadPartImages']);
            Route::delete('delete-part-photo/{part}/{photo}', [CreateWholesalePartController::class, 'deletePartPhoto']);
        });

        Route::prefix('list')->group(function() {
            Route::get('/', [ListWholesalePartsController::class, 'list']);
            Route::get('/document/{mvr}', [ListWholesalePartsController::class, 'document']);
            Route::get('/part/{part}', [ListWholesalePartsController::class, 'get']);
            Route::get('/part-ic-numbers/{part}', [ListWholesalePartsController::class, 'icNumbers']);
            Route::get('/makes', [ListWholesalePartsController::class, 'makes']);
            Route::get('/models/{make}', [ListWholesalePartsController::class, 'models']);
            Route::get('/years/{make}', [ListWholesalePartsController::class, 'years']);
            Route::get('/engines/{make}/{model}/{year}', [ListWholesalePartsController::class, 'engines']);
            Route::delete('/delete/{part}', [ListWholesalePartsController::class, 'delete']);
            Route::patch('/update-attributes/{car}', [ListWholesalePartsController::class, 'update']);
            Route::patch('/update-part-attributes/{part}', [ListWholesalePartsController::class, 'attributes']);
            Route::patch('/update-prices/{part}', [ListWholesalePartsController::class, 'prices']);
            Route::patch('/update-standard-prices/{nomenclatureBaseItemPdrCard}', [ListWholesalePartsController::class, 'updateStandardPrices']);
        });
    });
});
