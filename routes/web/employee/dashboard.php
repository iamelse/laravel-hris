<?php

use App\Http\Controllers\Web\Employee\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('employee.dashboard.index');