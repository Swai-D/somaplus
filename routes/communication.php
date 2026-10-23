<?php

use App\Http\Controllers\Communication\EventController;
use App\Http\Controllers\Communication\FeeReminderController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Communication\NoticeController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). The notice board and the
// calendar of events need `communication.view` to look at, `communication.manage`
// to change (the policies); the messages of the SMS gateway need `reminders.send`
// (checked by SmsMessagePolicy), because reminders reach guardians. Sending and
// changing also use `tenant.writable`. Filters travel in the query string (no
// personal data); what is chosen or typed travels in the POST body.

Route::get('communication/notices', [NoticeController::class, 'index'])->name('communication.notices.index');
Route::get('communication/notices/{notice}/edit', [NoticeController::class, 'edit'])
    ->whereNumber('notice')->name('communication.notices.edit');

Route::get('communication/events', [EventController::class, 'index'])->name('communication.events.index');
Route::get('communication/events/{event}/edit', [EventController::class, 'edit'])
    ->whereNumber('event')->name('communication.events.edit');

Route::get('communication/messages', [MessageController::class, 'index'])->name('communication.messages.index');

Route::get('communication/reminders', [FeeReminderController::class, 'index'])->name('communication.reminders.index');

Route::middleware('tenant.writable')->group(function () {
    Route::post('communication/notices', [NoticeController::class, 'store'])->name('communication.notices.store');
    Route::put('communication/notices/{notice}', [NoticeController::class, 'update'])
        ->whereNumber('notice')->name('communication.notices.update');
    Route::delete('communication/notices/{notice}', [NoticeController::class, 'destroy'])
        ->whereNumber('notice')->name('communication.notices.destroy');

    Route::post('communication/events', [EventController::class, 'store'])->name('communication.events.store');
    Route::put('communication/events/{event}', [EventController::class, 'update'])
        ->whereNumber('event')->name('communication.events.update');
    Route::delete('communication/events/{event}', [EventController::class, 'destroy'])
        ->whereNumber('event')->name('communication.events.destroy');

    Route::post('communication/reminders', [FeeReminderController::class, 'store'])
        ->middleware('throttle:10,1')->name('communication.reminders.store');
});
