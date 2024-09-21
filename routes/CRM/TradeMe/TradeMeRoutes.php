<?php

use App\Http\Controllers\CRM\TradeMe\TradeMeController;
use Illuminate\Support\Facades\Route;

Route::prefix('/trade-me')->group(function () {
    Route::get('auth-data', [TradeMeController::class, 'getAuthData']);
    Route::get('request-verification-url', [TradeMeController::class, 'getVerificationUrl']);
    Route::delete('delete-authorization', [TradeMeController::class, 'deleteAuthorization']);
    Route::patch('set-authorization', [TradeMeController::class, 'setAuthorization']);

    Route::prefix('/trademe-api')->group(function () {
        Route::get('categories', [TradeMeController::class, 'getCategories']);
        Route::get('sub-categories', [TradeMeController::class, 'getSubCategories']);
    });

    Route::prefix('/trademe-groups')->group(function () {
        Route::get('list', [TradeMeController::class, 'groupsList']);
        Route::post('create', [TradeMeController::class, 'groupCreate']);
        Route::post('delete/{group}', [TradeMeController::class, 'groupDelete']);
    });
});
