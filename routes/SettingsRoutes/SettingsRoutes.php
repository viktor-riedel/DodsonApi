<?php

use App\Http\Controllers\Settiings\NomenclatureCardController;
use App\Http\Controllers\Settiings\YardsController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function() {
    //card
    Route::get('/nomenclature-card', [NomenclatureCardController::class, 'index'])->name('settings.nomenclature-card');
    Route::put('/update-nomenclature-card', [NomenclatureCardController::class, 'update'])->name('settings.nomenclature-card.update');
    //yards
    Route::get('/yards', [YardsController::class, 'index'])->name('settings.yards.list');
    Route::post('/create-yard', [YardsController::class, 'create'])->name('settings.yards.create');
    Route::delete('/delete-yard/{yard}', [YardsController::class, 'delete'])->name('settings.yards.delete');
    Route::patch('/update-yard/{yard}', [YardsController::class, 'update'])->name('settings.yards.update');
});
