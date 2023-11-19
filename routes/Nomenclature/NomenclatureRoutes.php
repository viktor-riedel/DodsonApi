<?php

use App\Http\Controllers\Nomenclature\BaseItemController;
use App\Http\Controllers\Nomenclature\BaseItemPdrController;
use App\Http\Controllers\Nomenclature\NomenclatureController;
use Illuminate\Support\Facades\Route;

Route::prefix('nomenclature')->group(function() {
    Route::prefix('catalog')->group(function() {
       Route::get('/get-catalog-makes', [NomenclatureController::class, 'getCatalogMakes']);
       Route::get('/get-catalog-models/{make}', [NomenclatureController::class, 'getCatalogModels']);
       Route::get('/get-catalog-headers/{make}/{model}', [NomenclatureController::class, 'getCatalogMvrsHeaders']);
       Route::get('/get-catalog-pdr/{mvrId}', [NomenclatureController::class, 'getCatalogPdr']);
    });

    Route::prefix('base-item')->group(function() {
       Route::get('/list', [BaseItemController::class, 'index']);
       Route::get('/find/{baseItem}', [BaseItemController::class, 'edit']);
       Route::post('/save-base-item', [BaseItemController::class, 'save']);
       Route::patch('/update-base-item/{baseItem}', [BaseItemController::class, 'baseItemUpdate']);
    });

    Route::prefix('base-item-pdr')->group(function() {
       Route::patch('/update-base-item-pdr', [BaseItemPdrController::class, 'updateBasePdr']);
       Route::patch('/update-base-item-pdr-card/{pdrCard}', [BaseItemPdrController::class, 'update']);
    });
});
