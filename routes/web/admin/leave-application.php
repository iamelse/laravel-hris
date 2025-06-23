<?php

use App\Http\Controllers\Web\Admin\LeaveApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/leaves', [LeaveApplicationController::class, 'index'])->name('admin.leave.index');
Route::post('/leave/{id}/approve', [LeaveApplicationController::class, 'approve'])->name('admin.leave.approve');
Route::post('/leave/{id}/reject', [LeaveApplicationController::class, 'reject'])->name('admin.leave.reject');