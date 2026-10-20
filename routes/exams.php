<?php

use App\Http\Controllers\Exams\ExamController;
use App\Http\Controllers\Exams\GradingScaleController;
use App\Http\Controllers\Exams\MarkController;
use App\Http\Controllers\Exams\ResultController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Permissions are checked by the policies:
// looking needs `exams.view`, scales `grades.manage`, exams (and locking) `exams.manage`,
// marks `marks.enter`, results and report cards `results.view`. Routes that change data also use `tenant.writable`.

Route::get('grading-scales', [GradingScaleController::class, 'index'])->name('grading-scales.index');
Route::get('grading-scales/create', [GradingScaleController::class, 'create'])->name('grading-scales.create');
Route::get('grading-scales/{scale}/edit', [GradingScaleController::class, 'edit'])->name('grading-scales.edit')->whereNumber('scale');

Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
Route::get('exams/create', [ExamController::class, 'create'])->name('exams.create');
Route::get('exams/{exam}', [ExamController::class, 'show'])->name('exams.show')->whereNumber('exam');
Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit')->whereNumber('exam');
Route::get('exams/{exam}/marks', [MarkController::class, 'index'])->name('exams.marks.index')->whereNumber('exam');
Route::get('exams/{exam}/results', [ResultController::class, 'index'])->name('exams.results.index')->whereNumber('exam');
Route::get('students/{student}/report/{term}', [ResultController::class, 'report'])->name('students.report')->whereNumber(['student', 'term']);

Route::middleware('tenant.writable')->group(function () {
    Route::post('grading-scales', [GradingScaleController::class, 'store'])->name('grading-scales.store');
    Route::put('grading-scales/{scale}', [GradingScaleController::class, 'update'])->name('grading-scales.update')->whereNumber('scale');
    Route::post('grading-scales/{scale}/copy', [GradingScaleController::class, 'copy'])->name('grading-scales.copy')->whereNumber('scale');
    Route::delete('grading-scales/{scale}', [GradingScaleController::class, 'destroy'])->name('grading-scales.destroy')->whereNumber('scale');

    Route::post('exams', [ExamController::class, 'store'])->name('exams.store');
    Route::put('exams/{exam}', [ExamController::class, 'update'])->name('exams.update')->whereNumber('exam');
    Route::delete('exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy')->whereNumber('exam');
    Route::post('exams/{exam}/lock', [ExamController::class, 'lock'])->name('exams.lock')->whereNumber('exam');
    Route::post('exams/{exam}/unlock', [ExamController::class, 'unlock'])->name('exams.unlock')->whereNumber('exam');
    Route::post('exams/{exam}/marks', [MarkController::class, 'store'])->name('exams.marks.store')->whereNumber('exam');
});
