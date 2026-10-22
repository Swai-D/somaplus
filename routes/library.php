<?php

use App\Http\Controllers\Library\BookController;
use App\Http\Controllers\Library\BookIssueController;
use App\Http\Controllers\Library\MemberController;
use Illuminate\Support\Facades\Route;

// The library: its books, its members and the copies that went out. Reading needs
// `library.view`, changing needs `library.manage` (the policies) and a school that
// is not read-only.

Route::prefix('library')->name('library.')->group(function () {
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('index');
        Route::post('search', [BookController::class, 'search'])->name('search');
        Route::get('create', [BookController::class, 'create'])->name('create');
        Route::put('{book}', [BookController::class, 'update'])->name('update')->whereNumber('book');

        Route::middleware('tenant.writable')->group(function () {
            Route::post('/', [BookController::class, 'store'])->name('store');
            Route::delete('{book}', [BookController::class, 'destroy'])->name('destroy')->whereNumber('book');
        });

        Route::get('{book}/edit', [BookController::class, 'edit'])->name('edit')->whereNumber('book');
    });

    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::post('search', [MemberController::class, 'search'])->name('search');
        Route::get('create', [MemberController::class, 'create'])->name('create');
        Route::put('{member}', [MemberController::class, 'update'])->name('update')->whereNumber('member');

        Route::middleware('tenant.writable')->group(function () {
            Route::post('/', [MemberController::class, 'store'])->name('store');
            Route::delete('{member}', [MemberController::class, 'destroy'])->name('destroy')->whereNumber('member');
        });

        Route::get('{member}', [MemberController::class, 'show'])->name('show')->whereNumber('member');
        Route::get('{member}/card', [MemberController::class, 'card'])->name('card')->whereNumber('member');
        Route::get('{member}/edit', [MemberController::class, 'edit'])->name('edit')->whereNumber('member');
    });

    Route::prefix('issues')->name('issues.')->group(function () {
        Route::get('/', [BookIssueController::class, 'index'])->name('index');
        Route::post('search', [BookIssueController::class, 'search'])->name('search');
        Route::get('create', [BookIssueController::class, 'create'])->name('create');

        Route::middleware('tenant.writable')->group(function () {
            Route::post('/', [BookIssueController::class, 'store'])->name('store');
            Route::post('{book_issue}/return', [BookIssueController::class, 'takeBack'])->name('return')->whereNumber('book_issue');
        });
    });
});
