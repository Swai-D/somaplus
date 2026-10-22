<?php

use App\Http\Controllers\Family\ChildController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). The family pages are for a parent's own account (`family.portal`,
// held by the parent role only) and only ever show the children linked to that account (FamilyChildren): another
// child answers 404. They only read, so they also work in a read-only school.

Route::middleware('can:family.portal')->prefix('family')->name('family.')->group(function () {
    Route::get('/', [ChildController::class, 'index'])->name('index');
    Route::get('children/{student}', [ChildController::class, 'show'])->name('children.show')->whereNumber('student');
    Route::get('children/{student}/report/{term}', [ChildController::class, 'report'])->name('children.report')->whereNumber(['student', 'term']);
});
