<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprovalController;

/*
|--------------------------------------------------------------------------
| Approval Routes
|--------------------------------------------------------------------------
| Routes for managing financial and medical supply requests with multi-level approval
*/

Route::middleware(['auth'])->group(function () {
    // Main approvals dashboard (accessible to all authenticated users)
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    
    // Financial assistance requests
    Route::get('/approvals/financial/create', [ApprovalController::class, 'createFinancial'])->name('approvals.financial.create');
    Route::post('/approvals/financial', [ApprovalController::class, 'storeFinancial'])->name('approvals.financial.store');
    
    // Medical supplies requests
    Route::get('/approvals/medical/create', [ApprovalController::class, 'createMedical'])->name('approvals.medical.create');
    Route::post('/approvals/medical', [ApprovalController::class, 'storeMedical'])->name('approvals.medical.store');
    
    // Approval actions
    Route::post('/approvals/{type}/{id}/admin-approve', [ApprovalController::class, 'adminApprove'])->name('approvals.admin-approve');
    Route::post('/approvals/{type}/{id}/admin-reject', [ApprovalController::class, 'adminReject'])->name('approvals.admin-reject');
    Route::post('/approvals/{type}/{id}/superadmin-approve', [ApprovalController::class, 'superadminApprove'])->name('approvals.superadmin-approve');
    Route::post('/approvals/{type}/{id}/superadmin-reject', [ApprovalController::class, 'superadminReject'])->name('approvals.superadmin-reject');
    
    // View request details (API endpoint)
    Route::get('/approvals/{type}/{id}', [ApprovalController::class, 'show'])->name('approvals.show');
});
