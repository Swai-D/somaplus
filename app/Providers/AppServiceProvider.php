<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Menu;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Scoped, so the current tenant is never kept between requests or queued jobs.
        $this->app->scoped(TenantContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Production always sits behind HTTPS (Railway). Also covers links built without a request,
        // such as queued emails, which would otherwise follow the scheme of APP_URL.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // What the application layout (header, sidebar, banner) needs, from real data.
        View::composer('layouts.app', function ($view): void {
            $authenticated = Auth::user();
            $user = $authenticated instanceof User ? $authenticated : null;
            $context = $this->app->make(TenantContext::class);

            $view->with([
                'currentUser' => $user,
                'currentSchool' => $context->current(),
                'menu' => Menu::fromConfig()->build($user, request()),
                'roleLabel' => $user === null ? '' : ($user->isSuperAdmin()
                    ? __('layout.platform_admin')
                    : (string) $user->getRoleNames()->map(fn ($role) => __('roles.'.$role))->implode(', ')),
            ]);
        });
    }
}
