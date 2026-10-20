<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chooses the tenant for a request from its host. See config/tenancy.php.
 */
class ResolveTenant
{
    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        if (in_array($host, config('tenancy.central_domains', []), true)) {
            // Central domain: nothing carries over from an earlier request. Only when the Railway path entry
            // is switched on (TENANCY_ALLOW_PATH_ENTRY) does a school chosen with /t/{slug} apply here.
            $this->context->clear();

            if (config('tenancy.allow_path_entry') && $request->hasSession() && $slug = $request->session()->get('tenant_slug')) {
                $tenant = Tenant::query()->where('slug', $slug)->first();
                if ($tenant !== null) {
                    $this->context->set($tenant);
                }
            }

            return $next($request);
        }

        $tenant = $this->findTenant($host);

        abort_if($tenant === null, 404, __('tenancy.school_not_found'));

        $this->context->set($tenant);

        return $next($request);
    }

    private function findTenant(string $host): ?Tenant
    {
        $tenant = Tenant::query()->where('domain', $host)->first();

        if ($tenant !== null) {
            return $tenant;
        }

        $baseDomain = config('tenancy.base_domain');

        if (! is_string($baseDomain) || $baseDomain === '') {
            return null;
        }

        $suffix = '.'.strtolower($baseDomain);

        if (! str_ends_with($host, $suffix)) {
            return null;
        }

        $slug = substr($host, 0, -strlen($suffix));

        if ($slug === '' || str_contains($slug, '.')) {
            return null;
        }

        return Tenant::query()->where('slug', $slug)->first();
    }
}
