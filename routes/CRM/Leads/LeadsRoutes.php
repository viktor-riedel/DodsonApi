<?php

use App\Http\Controllers\CRM\Leads\LeadsController;
use Illuminate\Support\Facades\Route;

Route::prefix('/leads')->group(function () {
   Route::get('/', [LeadsController::class, 'list'])->name('leads.list');
   Route::get('/lead/{lead}', [LeadsController::class, 'lead'])->name('leads.lead');
   Route::get('/create-lead-data', [LeadsController::class, 'newLeadData'])->name('leads.create-data');
   Route::post('/create-lead', [LeadsController::class, 'create'])->name('leads.create');
   Route::put('/lead/{lead}/update', [LeadsController::class, 'update'])->name('leads.lead.update');
});
