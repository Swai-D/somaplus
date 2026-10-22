<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\Webhooks\AzamPayCallbackController;
use App\Http\Controllers\Webhooks\GepgWebhookController;
use App\Http\Controllers\Webhooks\PaymentWebhookController;
use App\Http\Controllers\Webhooks\SelcomWebhookController;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check for Railway (and any uptime monitor).
Route::get('/up', [HealthController::class, '__invoke']);

// Payment notices from a provider (central address only; the school is named in the path). Public and
// signed: the controller refuses anything whose HMAC signature does not check out. Not CSRF protected.
Route::post('/webhooks/payments/{tenant}', PaymentWebhookController::class)
    ->where('tenant', '[0-9A-Za-z]{26}')->middleware('throttle:120,1')->name('webhooks.payments');

Route::post('/webhooks/gepg/{tenant}', GepgWebhookController::class)
    ->where('tenant', '[0-9A-Za-z]{26}')->middleware('throttle:120,1')->name('webhooks.gepg');

Route::post('/webhooks/selcom/{tenant}/{step}', SelcomWebhookController::class)
    ->where(['tenant' => '[0-9A-Za-z]{26}', 'step' => 'lookup|validation|notification'])->middleware('throttle:240,1')->name('webhooks.selcom');

Route::post('/webhooks/azampay/{tenant}', AzamPayCallbackController::class)
    ->where('tenant', '[0-9A-Za-z]{26}')->middleware('throttle:120,1')->name('webhooks.azampay');

// Temporary tenant entry for hosts behind a proxy without wildcard subdomains
// (e.g. *.up.railway.app). Sets tenant in browser session, then redirects to login.
// Only on the central domain; never trusts X-Forwarded-Host.
if (config('tenancy.allow_path_entry')) {
    Route::middleware('web')->group(function () {
        Route::get('/t/{slug}', function (string $slug, Request $request) {
            abort_unless(in_array($request->getHost(), config('tenancy.central_domains', [])), 404);

            $tenant = TenantContext::runAsSystem(
                fn () => Tenant::where('slug', $slug)->first()
            );
            abort_if(! $tenant, 404);

            if (auth()->check() && auth()->user()->tenant_id !== $tenant->id) {
                abort(403);
            }

            $request->session()->regenerate();
            $request->session()->put('tenant_slug', $tenant->slug);

            return redirect()->route('login');
        })->where('slug', '[a-z0-9-]+')->middleware('throttle:20,1');

        Route::get('/t-exit', function (Request $request) {
            $request->session()->forget('tenant_slug');

            return redirect()->route('login');
        })->name('tenant.exit');
    });
}

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
    Route::get('/dashboard/teacher', TeacherDashboardController::class)->middleware('can:marks.enter')->name('teacher.dashboard');
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/help/{category}/{slug}', [HelpController::class, 'show'])
        ->where(['category' => '[a-z0-9-]+', 'slug' => '[a-z0-9-]+'])
        ->name('help.show');

    require __DIR__.'/academics.php';
    require __DIR__.'/students.php';
    require __DIR__.'/attendance.php';
    require __DIR__.'/exams.php';
    require __DIR__.'/fees.php';
    require __DIR__.'/settings.php';
    require __DIR__.'/communication.php';
    require __DIR__.'/platform.php';
    require __DIR__.'/family.php';
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
