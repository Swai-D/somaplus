<?php

use App\Http\Controllers\Attendance\AttendanceController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Everything needs `attendance.manage` (policy);
// saving also needs a school that is not read-only.

Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('summary', [AttendanceController::class, 'summary'])->name('summary');

    Route::middleware('tenant.writable')->group(function () {
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
    });
});
