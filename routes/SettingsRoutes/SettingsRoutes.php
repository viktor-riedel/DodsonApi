<?php

use App\Http\Controllers\Settiings\NomenclatureCardController;
use App\Http\Controllers\Settiings\YardsController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function() {
    //card
    Route::get('/nomenclature-card', [NomenclatureCardController::class, 'index']);
    Route::put('/update-nomenclature-card', [NomenclatureCardController::class, 'update']);
    //yards
    Route::get('/yards', [YardsController::class, 'index']);
    Route::post('/create-yard', [YardsController::class, 'create']);
    Route::delete('/delete-yard/{yard}', [YardsController::class, 'delete']);
    Route::patch('/update-yard/{yard}', [YardsController::class, 'update']);
});
