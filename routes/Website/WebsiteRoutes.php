<?php

use App\Http\Controllers\Website\RegisterController;
use App\Http\Controllers\Website\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::prefix('website')->middleware('throttle:api')->group(function() {
   Route::post('/send-contact-us', [WebsiteController::class, 'sendContactEmail']);
   Route::post('/register', [RegisterController::class, 'register']);
});
