<?php

use App\Http\Controllers\Web\Admin\ReimbursementController;
use Illuminate\Support\Facades\Route;

Route::get('/reimbursements', [ReimbursementController::class, 'index'])->name('admin.reimbursement.index');
Route::get('/reimbursement/{id}', [ReimbursementController::class, 'show'])->name('admin.reimbursement.show');
Route::post('/reimbursement/{id}/approve', [ReimbursementController::class, 'approve'])->name('admin.reimbursement.approve');
Route::post('/reimbursement/{id}/reject', [ReimbursementController::class, 'reject'])->name('admin.reimbursement.reject');