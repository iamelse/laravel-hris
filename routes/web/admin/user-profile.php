<?php

use App\Http\Controllers\Web\BackEnd\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/profile/{user}', [UserProfileController::class, 'edit'])->name('admin.user.profile.edit');
Route::put('/profile/{user}', [UserProfileController::class, 'update'])->name('admin.user.profile.update');

Route::put('/profile/{user}/password', [UserProfileController::class, 'updatePassword'])->name('admin.user.profile.update.password');