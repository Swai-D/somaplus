<?php

use App\Http\Controllers\Settings\AdmissionSettingController;
use App\Http\Controllers\Settings\IntegrationController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Only `settings.manage` (owners) may look at or change
// integrations and admission numbers (checked by TenantIntegrationPolicy and AdmissionSettingPolicy). Saving also uses `tenant.writable`.

Route::get('settings/integrations', [IntegrationController::class, 'index'])->name('settings.integrations.index');

Route::get('settings/admission-numbers', [AdmissionSettingController::class, 'index'])->name('settings.admission.index');

Route::middleware('tenant.writable')->group(function () {
    Route::put('settings/admission-numbers', [AdmissionSettingController::class, 'update'])->name('settings.admission.update');
    Route::put('settings/integrations/{channel}', [IntegrationController::class, 'update'])
        ->where('channel', '[a-z_]+')->name('settings.integrations.update');
});
