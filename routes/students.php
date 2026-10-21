<?php

use App\Http\Controllers\Students\EnrollmentController;
use App\Http\Controllers\Students\GuardianController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\StudentImportController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Reading needs `students.view`,
// changing needs `students.manage` (policy) and a school that is not read-only.

Route::prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index');
    Route::post('search', [StudentController::class, 'search'])->name('search');
    Route::get('create', [StudentController::class, 'create'])->name('create');
    Route::get('import', [StudentImportController::class, 'create'])->name('import');
    Route::get('import/template', [StudentImportController::class, 'template'])->name('import.template');
    Route::get('{student}', [StudentController::class, 'show'])->name('show')->whereNumber('student');
    Route::get('{student}/edit', [StudentController::class, 'edit'])->name('edit')->whereNumber('student');

    Route::middleware('tenant.writable')->group(function () {
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::post('import', [StudentImportController::class, 'store'])->name('import.store');
        Route::put('{student}', [StudentController::class, 'update'])->name('update')->whereNumber('student');
        Route::post('{student}/leave', [StudentController::class, 'leave'])->name('leave')->whereNumber('student');
        Route::post('{student}/reinstate', [StudentController::class, 'reinstate'])->name('reinstate')->whereNumber('student');
        Route::post('{student}/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store')->whereNumber('student');
        Route::post('{student}/guardians', [GuardianController::class, 'store'])->name('guardians.store')->whereNumber('student');
        Route::delete('{student}/guardians/{guardian}', [GuardianController::class, 'destroy'])->name('guardians.destroy')->whereNumber(['student', 'guardian']);
    });
});
