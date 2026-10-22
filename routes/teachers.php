<?php

use App\Http\Controllers\Teachers\TeacherController;
use Illuminate\Support\Facades\Route;

// The teachers of the school, on their own. Reading needs `teachers.view`, changing needs
// `teachers.manage` (TeacherPolicy) and a school that is not read-only.

Route::prefix('teachers')->name('teachers.')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('index');
    Route::post('search', [TeacherController::class, 'search'])->name('search');
    Route::get('create', [TeacherController::class, 'create'])->name('create');
    Route::get('{teacher}', [TeacherController::class, 'show'])->name('show')->whereNumber('teacher');
    Route::get('{teacher}/edit', [TeacherController::class, 'edit'])->name('edit')->whereNumber('teacher');

    Route::middleware('tenant.writable')->group(function () {
        Route::post('/', [TeacherController::class, 'store'])->name('store');
        Route::put('{teacher}', [TeacherController::class, 'update'])->name('update')->whereNumber('teacher');
        Route::post('{teacher}/status', [TeacherController::class, 'status'])->name('status')->whereNumber('teacher');
        Route::delete('{teacher}', [TeacherController::class, 'destroy'])->name('destroy')->whereNumber('teacher');
    });
});
