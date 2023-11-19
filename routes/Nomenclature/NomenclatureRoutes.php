<?php

use App\Http\Controllers\Nomenclature\BaseItemController;
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
       Route::post('/save-base-item', [BaseItemController::class, 'save']);
    });
});
