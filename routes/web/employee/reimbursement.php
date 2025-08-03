<?php

use App\Http\Controllers\Web\Employee\ReimbursementController;
use Illuminate\Support\Facades\Route;

Route::prefix('employee/reimbursement')->middleware(['auth'])->name('employee.reimbursement.')->group(function () {
    Route::get('/', [ReimbursementController::class, 'index'])->name('index');
    Route::get('/create', [ReimbursementController::class, 'create'])->name('create');
    Route::post('/', [ReimbursementController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ReimbursementController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ReimbursementController::class, 'update'])->name('update');
    Route::delete('/{id}', [ReimbursementController::class, 'destroy'])->name('destroy');
});