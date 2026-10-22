<?php

use App\Http\Controllers\Communication\FeeReminderController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Only `reminders.send` (owner, accountant) may look at or send fee
// reminders (checked by SmsMessagePolicy). Sending also uses `tenant.writable`. Filters travel in the query string
// (no personal data); the chosen invoices travel in the POST body.

Route::get('communication/reminders', [FeeReminderController::class, 'index'])->name('communication.reminders.index');

Route::middleware('tenant.writable')->group(function () {
    Route::post('communication/reminders', [FeeReminderController::class, 'store'])->middleware('throttle:10,1')->name('communication.reminders.store');
});
