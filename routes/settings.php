<?php

use App\Http\Controllers\Settings\AdmissionSettingController;
use App\Http\Controllers\Settings\IntegrationController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SchoolProfileController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Only `settings.manage` (owners) may look at or change
// integrations, admission numbers, users and custom roles (checked by TenantIntegrationPolicy and AdmissionSettingPolicy). Saving also uses `tenant.writable`.

Route::get('settings/integrations', [IntegrationController::class, 'index'])->name('settings.integrations.index');

Route::get('settings/admission-numbers', [AdmissionSettingController::class, 'index'])->name('settings.admission.index');

Route::get('settings/school', [SchoolProfileController::class, 'index'])->name('settings.school.index');

Route::get('settings/users', [UserController::class, 'index'])->name('settings.users.index');
Route::get('settings/users/create', [UserController::class, 'create'])->name('settings.users.create');
Route::get('settings/users/{user}/edit', [UserController::class, 'edit'])->name('settings.users.edit')->whereNumber('user');

// Custom roles are a feature of the Enterprise plan (`plan:custom_roles`).
Route::middleware('plan:custom_roles')->group(function () {
    Route::get('settings/roles', [RoleController::class, 'index'])->name('settings.roles.index');
    Route::get('settings/roles/create', [RoleController::class, 'create'])->name('settings.roles.create');
    Route::get('settings/roles/{role}/edit', [RoleController::class, 'edit'])->name('settings.roles.edit')->whereNumber('role');
});

Route::middleware('tenant.writable')->group(function () {
    Route::put('settings/school', [SchoolProfileController::class, 'update'])->name('settings.school.update');
    Route::post('settings/users', [UserController::class, 'store'])->name('settings.users.store');
    Route::put('settings/users/{user}', [UserController::class, 'update'])->name('settings.users.update')->whereNumber('user');

    Route::middleware('plan:custom_roles')->group(function () {
        Route::post('settings/roles', [RoleController::class, 'store'])->name('settings.roles.store');
        Route::put('settings/roles/{role}', [RoleController::class, 'update'])->name('settings.roles.update')->whereNumber('role');
        Route::delete('settings/roles/{role}', [RoleController::class, 'destroy'])->name('settings.roles.destroy')->whereNumber('role');
    });

    Route::put('settings/admission-numbers', [AdmissionSettingController::class, 'update'])->name('settings.admission.update');
    Route::put('settings/integrations/{channel}', [IntegrationController::class, 'update'])
        ->where('channel', '[a-z_]+')->name('settings.integrations.update');
});
