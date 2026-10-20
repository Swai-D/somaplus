<?php

use App\Http\Controllers\Fees\FeeStructureController;
use App\Http\Controllers\Fees\InvoiceController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Looking needs `fees.view`; changing structures,
// making invoices and setting control numbers needs `fees.manage` (checked by the policies).
// Routes that change data also use `tenant.writable`. Filters travel in the query string (no personal data).

Route::get('fees/structures', [FeeStructureController::class, 'index'])->name('fees.structures.index');
Route::get('fees/structures/create', [FeeStructureController::class, 'create'])->name('fees.structures.create');
Route::get('fees/structures/{structure}', [FeeStructureController::class, 'show'])->name('fees.structures.show')->whereNumber('structure');
Route::get('fees/structures/{structure}/edit', [FeeStructureController::class, 'edit'])->name('fees.structures.edit')->whereNumber('structure');
Route::get('fees/invoices', [InvoiceController::class, 'index'])->name('fees.invoices.index');
Route::get('fees/invoices/{invoice}', [InvoiceController::class, 'show'])->name('fees.invoices.show')->whereNumber('invoice');

Route::middleware('tenant.writable')->group(function () {
    Route::post('fees/structures', [FeeStructureController::class, 'store'])->name('fees.structures.store');
    Route::put('fees/structures/{structure}', [FeeStructureController::class, 'update'])->name('fees.structures.update')->whereNumber('structure');
    Route::delete('fees/structures/{structure}', [FeeStructureController::class, 'destroy'])->name('fees.structures.destroy')->whereNumber('structure');
    Route::post('fees/structures/{structure}/invoices', [FeeStructureController::class, 'generate'])->name('fees.structures.generate')->whereNumber('structure');
    Route::put('fees/invoices/{invoice}/control-number', [InvoiceController::class, 'controlNumber'])->name('fees.invoices.control-number')->whereNumber('invoice');
});
