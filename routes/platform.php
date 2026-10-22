<?php

use App\Http\Controllers\Platform\SchoolController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). `platform.admin` answers 404 to anyone who is not a platform
// administrator on a central address. The school in the path is looked up by id outside any school context
// (central address, no tenant), so route binding sees every school: this is the one reviewed place that does.

Route::middleware('platform.admin')->prefix('platform')->group(function () {
    Route::get('schools', [SchoolController::class, 'index'])->name('platform.schools.index');
    Route::get('schools/create', [SchoolController::class, 'create'])->name('platform.schools.create');
    Route::post('schools', [SchoolController::class, 'store'])->middleware('throttle:20,1')->name('platform.schools.store');
    Route::get('schools/{tenant}', [SchoolController::class, 'edit'])->where('tenant', '[0-9A-Za-z]{26}')->name('platform.schools.edit');
    Route::put('schools/{tenant}', [SchoolController::class, 'update'])->where('tenant', '[0-9A-Za-z]{26}')->name('platform.schools.update');
});
