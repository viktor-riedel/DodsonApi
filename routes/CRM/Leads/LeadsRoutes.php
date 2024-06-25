<?php

use App\Http\Controllers\CRM\Leads\LeadsController;
use Illuminate\Support\Facades\Route;

Route::prefix('/leads')->group(function () {
   Route::get('/', [LeadsController::class, 'list']);
   Route::get('/lead/{lead}', [LeadsController::class, 'lead']);
   Route::get('/create-lead-data', [LeadsController::class, 'newLeadData']);
   Route::post('/create-lead', [LeadsController::class, 'create']);
   Route::put('/lead/{lead}/update', [LeadsController::class, 'update']);
});
