<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Railway path entry: on the central address, and only when TENANCY_ALLOW_PATH_ENTRY is on, a school chosen with
 * /t/{slug} (remembered in the browser's session) becomes the tenant, so its users can sign in without their own
 * domain. This must run after StartSession (the slug lives in the session) and before authentication and route
 * model binding (both query tenant-scoped models), which `bootstrap/app.php` guarantees through the middleware
 * priority list. It never applies on a school's own address, and never trusts a header.
 */
class ApplyPathEntryTenant
{
    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (config('tenancy.allow_path_entry')
            && in_array(strtolower($request->getHost()), config('tenancy.central_domains', []), true)
            && $request->hasSession()
            && is_string($slug = $request->session()->get('tenant_slug'))
            && $slug !== '') {
            $tenant = Tenant::query()->where('slug', $slug)->first();

            if ($tenant !== null) {
                $this->context->set($tenant);
            }
        }

        return $next($request);
    }
}
