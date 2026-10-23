<?php

use App\Http\Controllers\Timetable\PeriodController;
use App\Http\Controllers\Timetable\TimetableController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Looking needs `timetable.view`, changing the
// lessons and the lesson slots needs `timetable.manage` (policies), and saving also needs a school
// that is not read-only.

Route::prefix('timetable')->name('timetable.')->group(function () {
    Route::get('/', [TimetableController::class, 'index'])->name('index');
    Route::get('teachers', [TimetableController::class, 'teachers'])->name('teachers');
    Route::get('mine', [TimetableController::class, 'mine'])->name('mine');
    Route::get('entries/{entry}/edit', [TimetableController::class, 'edit'])->name('entries.edit')->whereNumber('entry');
    Route::get('periods', [PeriodController::class, 'index'])->name('periods.index');
    Route::get('periods/{period}/edit', [PeriodController::class, 'edit'])->name('periods.edit')->whereNumber('period');

    Route::middleware('tenant.writable')->group(function () {
        Route::post('entries', [TimetableController::class, 'store'])->name('entries.store');
        Route::put('entries/{entry}', [TimetableController::class, 'update'])->name('entries.update')->whereNumber('entry');
        Route::delete('entries/{entry}', [TimetableController::class, 'destroy'])->name('entries.destroy')->whereNumber('entry');
        Route::post('copy', [TimetableController::class, 'copy'])->name('copy');

        Route::post('periods', [PeriodController::class, 'store'])->name('periods.store');
        Route::put('periods/{period}', [PeriodController::class, 'update'])->name('periods.update')->whereNumber('period');
        Route::delete('periods/{period}', [PeriodController::class, 'destroy'])->name('periods.destroy')->whereNumber('period');
    });
});
