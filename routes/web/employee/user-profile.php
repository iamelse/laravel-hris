<?php

use App\Http\Controllers\Web\Admin\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/profile/{user}', [UserProfileController::class, 'edit'])->name('employee.user.profile.edit');
Route::put('/profile/{user}', [UserProfileController::class, 'update'])->name('employee.user.profile.update');

Route::put('/profile/{user}/password', [UserProfileController::class, 'updatePassword'])->name('employee.user.profile.update.password');