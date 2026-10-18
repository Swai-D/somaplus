<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware `tenant.writable`: a school whose subscription has lapsed
 * into read-only can still view its data, but cannot change it.
 */
class EnsureTenantWritable
{
    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->context->current();

        if ($tenant !== null && $tenant->isReadOnly() && ! $request->isMethodSafe()) {
            abort(403, __('tenancy.read_only'));
        }

        return $next($request);
    }
}
