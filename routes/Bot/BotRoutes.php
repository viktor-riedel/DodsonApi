<?php

use App\Http\Controllers\Bot\BotController;
use Illuminate\Support\Facades\Route;

Route::prefix('bot')->group(function () {
    Route::get('/stock/{car}', [BotController::class, 'car']);
    Route::get('/stock', [BotController::class, 'stock']);
});
