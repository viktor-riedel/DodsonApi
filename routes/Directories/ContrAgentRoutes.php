<?php

use App\Http\Controllers\Directories\ContrAgents\ContrAgentsController;
use Illuminate\Support\Facades\Route;

Route::prefix('contr-agents')->middleware(['auth:sanctum', 'is_web_user'])->group(function() {
   Route::get('/list', [ContrAgentsController::class, 'list']);
   Route::get('/get/{contrAgent}', [ContrAgentsController::class, 'get']);
   Route::post('/create', [ContrAgentsController::class, 'create']);
   Route::patch('/update/{contrAgent}', [ContrAgentsController::class, 'update']);
   Route::delete('/delete/{contrAgent}', [ContrAgentsController::class, 'delete']);
});
