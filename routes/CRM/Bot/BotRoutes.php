<?php

use App\Http\Controllers\CRM\Bot\BotController;
use Illuminate\Support\Facades\Route;

Route::prefix('bot')->group(function () {
    Route::post('/send-message', [BotController::class, 'sendMessage']);
});
