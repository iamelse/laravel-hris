<?php

use App\Http\Controllers\Web\Employee\LeaveApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('leaves')->name('employee.leave.')->group(function () {
    // Menampilkan semua pengajuan cuti milik employee
    Route::get('/', [LeaveApplicationController::class, 'index'])->name('index');

    // Form untuk membuat pengajuan cuti baru
    Route::get('/create', [LeaveApplicationController::class, 'create'])->name('create');

    // Menyimpan data pengajuan cuti baru
    Route::post('/store', [LeaveApplicationController::class, 'store'])->name('store');

    // Form edit untuk pengajuan cuti yang masih pending
    Route::get('/{leave}/edit', [LeaveApplicationController::class, 'edit'])->name('edit');

    // Update data pengajuan cuti (masih pending)
    Route::put('/{leave}/update', [LeaveApplicationController::class, 'update'])->name('update');

    // Hapus pengajuan cuti (hanya jika masih pending)
    Route::delete('/{leave}/destroy', [LeaveApplicationController::class, 'destroy'])->name('destroy');
});