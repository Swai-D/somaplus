<?php

use App\Http\Controllers\Students\EnrollmentController;
use App\Http\Controllers\Students\GuardianController;
use App\Http\Controllers\Students\PortalAccessController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\StudentIdCardController;
use App\Http\Controllers\Students\StudentImportController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Reading needs `students.view`,
// changing needs `students.manage` (policy) and a school that is not read-only. Printing ID cards needs
// `students.manage` too but changes nothing, so it also works in a read-only school.

Route::prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index');
    Route::post('search', [StudentController::class, 'search'])->name('search');
    Route::get('create', [StudentController::class, 'create'])->name('create');
    Route::get('import', [StudentImportController::class, 'create'])->name('import');
    Route::get('import/template', [StudentImportController::class, 'template'])->name('import.template');
    Route::get('id-cards', [StudentIdCardController::class, 'index'])->name('id-cards');
    Route::post('id-cards', [StudentIdCardController::class, 'print'])->name('id-cards.print');
    Route::get('{student}/id-card', [StudentIdCardController::class, 'show'])->name('id-card')->whereNumber('student');
    Route::get('{student}', [StudentController::class, 'show'])->name('show')->whereNumber('student');
    Route::get('{student}/edit', [StudentController::class, 'edit'])->name('edit')->whereNumber('student');

    Route::middleware('tenant.writable')->group(function () {
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::post('import', [StudentImportController::class, 'store'])->name('import.store');
        Route::put('{student}', [StudentController::class, 'update'])->name('update')->whereNumber('student');
        Route::post('{student}/leave', [StudentController::class, 'leave'])->name('leave')->whereNumber('student');
        Route::post('{student}/reinstate', [StudentController::class, 'reinstate'])->name('reinstate')->whereNumber('student');
        Route::post('{student}/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store')->whereNumber('student');
        Route::post('{student}/guardians', [GuardianController::class, 'linkToStudent'])->name('guardians.store')->whereNumber('student');
        Route::delete('{student}/guardians/{guardian}', [GuardianController::class, 'unlinkFromStudent'])->name('guardians.destroy')->whereNumber(['student', 'guardian']);
    });
});

// The guardians of the school, on their own. Reading needs `students.view`, changing needs
// `students.manage` (GuardianPolicy) and a school that is not read-only.
Route::prefix('guardians')->name('guardians.')->group(function () {
    Route::get('/', [GuardianController::class, 'index'])->name('index');
    Route::get('add', [GuardianController::class, 'add'])->name('add');
    Route::get('{guardian}', [GuardianController::class, 'show'])->name('show')->whereNumber('guardian');

    Route::middleware('tenant.writable')->group(function () {
        Route::post('/', [GuardianController::class, 'store'])->name('store');
        Route::put('{guardian}', [GuardianController::class, 'update'])->name('update')->whereNumber('guardian');
        Route::delete('{guardian}', [GuardianController::class, 'destroy'])->name('destroy')->whereNumber('guardian');
        Route::post('{guardian}/portal', [PortalAccessController::class, 'store'])->name('portal.store')->whereNumber('guardian');
        Route::delete('{guardian}/portal', [PortalAccessController::class, 'destroy'])->name('portal.destroy')->whereNumber('guardian');
        Route::post('{guardian}/students', [GuardianController::class, 'addStudent'])->name('students.store')->whereNumber('guardian');
        Route::delete('{guardian}/students/{student}', [GuardianController::class, 'removeStudent'])->name('students.destroy')->whereNumber(['guardian', 'student']);
    });
});
