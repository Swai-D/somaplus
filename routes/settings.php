<?php

use App\Http\Controllers\Settings\IntegrationController;
use Illuminate\Support\Facades\Route;

// Loaded inside the `auth` group (routes/web.php). Only `settings.manage` (owners) may look at or change
// integrations (checked by TenantIntegrationPolicy). Saving also uses `tenant.writable`.

Route::get('settings/integrations', [IntegrationController::class, 'index'])->name('settings.integrations.index');

Route::middleware('tenant.writable')->group(function () {
    Route::put('settings/integrations/{channel}', [IntegrationController::class, 'update'])
        ->where('channel', '[a-z_]+')->name('settings.integrations.update');
});
