<?php

use App\Http\Controllers\Web\BackEnd\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [UserController::class, 'index'])->name('admin.user.index');
Route::get('/user/create', [UserController::class, 'create'])->name('admin.user.create');
Route::post('/user/store', [UserController::class, 'store'])->name('admin.user.store');
Route::get('/user/{user:username}', [UserController::class, 'edit'])->name('admin.user.edit');
Route::put('/user/{user:username}', [UserController::class, 'update'])->name('admin.user.update');
Route::delete('/user/{user:username}', [UserController::class, 'destroy'])->name('admin.user.destroy');
Route::get('/user/mass/destroy', [UserController::class, 'massDestroy'])->name('admin.user.mass.destroy');