<?php

use App\Http\Controllers\Academics\AcademicYearController;
use App\Http\Controllers\Academics\SchoolClassController;
use App\Http\Controllers\Academics\StreamController;
use App\Http\Controllers\Academics\SubjectController;
use App\Http\Controllers\Academics\TermController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Reading needs `academics.view`,
// changing needs `academics.manage` (policies) and a school that is not read-only.

Route::prefix('academics')->name('academics.')->group(function () {
    Route::get('years', [AcademicYearController::class, 'index'])->name('years.index');
    Route::get('years/{year}', [AcademicYearController::class, 'show'])->name('years.show');
    Route::get('years/{year}/edit', [AcademicYearController::class, 'edit'])->name('years.edit');
    Route::get('terms/{term}/edit', [TermController::class, 'edit'])->name('terms.edit');

    Route::get('classes', [SchoolClassController::class, 'index'])->name('classes.index');
    Route::get('classes/{schoolClass}', [SchoolClassController::class, 'show'])->name('classes.show');
    Route::get('classes/{schoolClass}/edit', [SchoolClassController::class, 'edit'])->name('classes.edit');
    Route::get('streams/{stream}/edit', [StreamController::class, 'edit'])->name('streams.edit');

    Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');

    Route::middleware('tenant.writable')->group(function () {
        Route::post('years', [AcademicYearController::class, 'store'])->name('years.store');
        Route::put('years/{year}', [AcademicYearController::class, 'update'])->name('years.update');
        Route::delete('years/{year}', [AcademicYearController::class, 'destroy'])->name('years.destroy');
        Route::post('years/{year}/terms', [TermController::class, 'store'])->name('terms.store');
        Route::put('terms/{term}', [TermController::class, 'update'])->name('terms.update');
        Route::delete('terms/{term}', [TermController::class, 'destroy'])->name('terms.destroy');

        Route::post('classes', [SchoolClassController::class, 'store'])->name('classes.store');
        Route::put('classes/{schoolClass}', [SchoolClassController::class, 'update'])->name('classes.update');
        Route::delete('classes/{schoolClass}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');
        Route::post('classes/{schoolClass}/streams', [StreamController::class, 'store'])->name('streams.store');
        Route::put('streams/{stream}', [StreamController::class, 'update'])->name('streams.update');
        Route::delete('streams/{stream}', [StreamController::class, 'destroy'])->name('streams.destroy');

        Route::post('subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::put('subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    });
});
