<?php

use Illuminate\Support\Facades\Route;

/**
 * Web routes auth
 */
Route::prefix('auth')->group(function () {
    include __DIR__.'/web/auth/auth.php';
});

/**
 * Web routes admin
 */
Route::prefix('admin')->middleware(['is.auth', 'is.admin'])->group(function () {
    include __DIR__ .'/web/admin/dashboard.php';

    include __DIR__ .'/web/admin/leave-application.php';

    include __DIR__ .'/web/admin/reimbursement.php';

    include __DIR__ . '/web/admin/user.php';

    include __DIR__ . '/web/admin/user-profile.php';
});

/**
 * Web routes employee
 */
Route::prefix('employee')->middleware(['is.auth', 'is.employee'])->group(function () {
    include __DIR__ .'/web/employee/dashboard.php';

    include __DIR__ .'/web/employee/leave-application.php';

    include __DIR__ . '/web/employee/user-profile.php';
});


include __DIR__ . '/web/frontend/web.php';
include __DIR__ .'/dev-idcloudhost.php';
