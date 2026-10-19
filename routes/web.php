<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

// Sign-in, sign-out and the language switch never use `tenant.writable`:
// a read-only school can still sign in and read.
Route::post('/locale', LocaleController::class)->name('locale.update');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/help/{category}/{slug}', [HelpController::class, 'show'])
        ->where(['category' => '[a-z0-9-]+', 'slug' => '[a-z0-9-]+'])
        ->name('help.show');

    require __DIR__.'/academics.php';
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
