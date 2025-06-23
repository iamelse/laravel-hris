<?php

use Illuminate\Support\Facades\Route;

/**
 * Web routes auth
 */
Route::prefix('auth')->group(function () {
    include __DIR__.'/web/auth/auth.php';
});

/**
 * Web routes backend
 */
Route::prefix('admin')->middleware('is.auth')->group(function () {
    include __DIR__ .'/web/admin/dashboard.php';

    include __DIR__ .'/web/admin/leave-application.php';

    include __DIR__ . '/web/admin/user.php';

    include __DIR__ . '/web/admin/user-profile.php';
});


include __DIR__ . '/web/frontend/web.php';
include __DIR__ .'/dev-idcloudhost.php';
