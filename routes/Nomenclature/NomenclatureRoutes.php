<?php

use App\Http\Controllers\Nomenclature\BaseItemController;
use App\Http\Controllers\Nomenclature\BaseItemPdrController;
use App\Http\Controllers\Nomenclature\BaseItemPdrPositionController;
use App\Http\Controllers\Nomenclature\FileUploadsController;
use App\Http\Controllers\Nomenclature\NomenclatureController;
use App\Http\Controllers\Nomenclature\PartsListController;
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
       Route::delete('/delete-base-item/{baseItem}', [BaseItemController::class, 'baseItemDelete']);
    });

    Route::prefix('base-item-pdr')->group(function() {
       Route::patch('/update-base-item-pdr', [BaseItemPdrController::class, 'updateBasePdr']);
    });

    Route::prefix('base-item-pdr-position')->group(function() {
        Route::get('/positions/{baseItemPdr}', [BaseItemPdrPositionController::class, 'list']);
        Route::get('/item-position/{itemPosition}', [BaseItemPdrPositionController::class, 'loadItemPosition']);
        Route::post('/add-position/{baseItemPdr}', [BaseItemPdrPositionController::class, 'create']);
        Route::delete('/delete-position/{baseItemPdrPosition}', [BaseItemPdrPositionController::class, 'delete']);
        Route::patch('/update-position-card/{baseItemPdrPosition}', [BaseItemPdrPositionController::class, 'update']);
        Route::patch('/update-position/{baseItemPdrPosition}', [BaseItemPdrPositionController::class, 'updatePosition']);
    });

    Route::prefix('nomenclature-uploads')->group(function() {
        Route::post('/upload-photo/{baseItemPdrPosition}', [FileUploadsController::class, 'addPhotoToBaseItemPosition']);
        Route::delete('/delete-photo/{baseItemPdrPositionPhoto}', [FileUploadsController::class, 'deleteBaseItemPosition']);
    });

    Route::prefix('parts-list')->group(function() {
        Route::get('/', [PartsListController::class, 'getDefaultPartsList']);
    });
});
