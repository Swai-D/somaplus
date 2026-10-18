<?php

use App\Http\Controllers\Auth\LoginController;
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
});

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/help/{article}', [HelpController::class, 'show'])->name('help.show');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
