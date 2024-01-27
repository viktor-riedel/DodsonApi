<?php

use App\Http\Controllers\Nomenclature\BaseItemController;
use App\Http\Controllers\Nomenclature\BaseItemModificationsController;
use App\Http\Controllers\Nomenclature\BaseItemPdrController;
use App\Http\Controllers\Nomenclature\BaseItemPdrPositionController;
use App\Http\Controllers\Nomenclature\BaseItemsSearchController;
use App\Http\Controllers\Nomenclature\FileUploadsController;
use App\Http\Controllers\Nomenclature\NomenclatureController;
use App\Http\Controllers\Nomenclature\PartsListController;
use Illuminate\Support\Facades\Route;

Route::prefix('nomenclature')->middleware('auth:sanctum')->group(function() {
    Route::prefix('catalog')->group(function() {
       Route::get('/get-catalog-makes', [NomenclatureController::class, 'getCatalogMakes']);
       Route::get('/get-catalog-models/{make}', [NomenclatureController::class, 'getCatalogModels']);
       Route::get('/get-catalog-generations/{model}', [NomenclatureController::class, 'getCatalogGenerations']);
       Route::get('/get-catalog-headers/{make}/{model}', [NomenclatureController::class, 'getCatalogMvrsHeaders']);
       Route::get('/get-catalog-pdr/{mvrId}', [NomenclatureController::class, 'getCatalogPdr']);
    });

    Route::prefix('base-item')->group(function() {
       Route::get('/makes', [BaseItemController::class, 'makes']);
       Route::get('/models/{make}', [BaseItemController::class, 'models']);
       Route::get('/generations/{make}/{model}', [BaseItemController::class, 'generations']);
       Route::get('/find/{baseItem}', [BaseItemController::class, 'edit']);
       Route::get('/find-by-ic', [BaseItemController::class, 'findByIcNumber']);
       Route::post('/save-base-item', [BaseItemController::class, 'save']);
       Route::post('/save-base-item-pdr/{baseItem}', [BaseItemController::class, 'saveItemPdr']);
       Route::patch('/update-base-item/{baseItem}', [BaseItemController::class, 'baseItemUpdate']);
       Route::delete('/delete-base-item/{baseItem}', [BaseItemController::class, 'baseItemDelete']);
       Route::prefix('/search')->group(function() {
          Route::get('/makes', [BaseItemsSearchController::class, 'makes']);
          Route::get('/models/{make?}', [BaseItemsSearchController::class, 'models']);
          Route::get('/generations/{make?}/{model?}', [BaseItemsSearchController::class, 'generations']);
          Route::get('/headers/{make?}/{model?}/{generation?}', [BaseItemsSearchController::class, 'headers']);
       });
       Route::prefix('modifications')->group(function() {
           Route::get('/{nomenclatureBaseItemPdrPosition}/modifications', [BaseItemModificationsController::class, 'modifications']);
           Route::post('/{nomenclatureBaseItemPosition}/update', [BaseItemModificationsController::class, 'update']);
           Route::prefix('global')->group(function() {
               Route::get('/{nomenclatureBaseItem}/ic-list', [BaseItemModificationsController::class, 'icList']);
               Route::post('/{nomenclatureBaseItem}/ic-list', [BaseItemModificationsController::class, 'updateModifications']);
           });
       });
    });

    Route::prefix('base-item-pdr')->group(function() {
       Route::patch('/update-base-item-pdr', [BaseItemPdrController::class, 'updateBasePdr']);
    });

    Route::prefix('base-item-pdr-position')->group(function() {
        Route::get('/positions/{baseItemPdr}', [BaseItemPdrPositionController::class, 'list']);
        Route::get('/ic-list/{baseItemPdr}', [BaseItemPdrPositionController::class, 'icList']);
        Route::get('/list/{baseItemPdr}', [BaseItemPdrPositionController::class, 'listView']);
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
        Route::patch('/update/{partList}', [PartsListController::class, 'updatePart']);
        Route::post('/create', [PartsListController::class, 'createPart']);
        Route::delete('/delete/{partList}', [PartsListController::class, 'deletePart']);
        Route::post('/add-item/{partList}', [PartsListController::class, 'addPart']);
    });
});
