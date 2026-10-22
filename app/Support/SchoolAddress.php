<?php

namespace App\Support;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;

/**
 * Where a school is reached: its own domain, or `<slug>.<base domain>`. A school can be addressed this way only when
 * it has its own domain or the platform has wildcard subdomains (not the Railway path-entry set-up).
 */
final class SchoolAddress
{
    public function __construct(private readonly TenantContext $context, private readonly Request $request) {}

    public function isReachable(Tenant $school): bool
    {
        return $school->domain !== null || ! config('tenancy.allow_path_entry');
    }

    /** Scheme, host and port of a school, no trailing slash. */
    public function root(Tenant $school): string
    {
        $host = $school->domain ?? $school->slug.'.'.config('tenancy.base_domain');
        $port = $this->request->getPort();

        return $this->request->getScheme().'://'.$host.(in_array($port, [80, 443], true) ? '' : ':'.$port);
    }

    /** The current school's root when this request came in on a central address, otherwise null (use the request's own). */
    public function rootForCentralRequest(): ?string
    {
        $school = $this->context->current();

        if ($school === null || ! in_array(strtolower($this->request->getHost()), config('tenancy.central_domains', []), true) || ! $this->isReachable($school)) {
            return null;
        }

        return $this->root($school);
    }
}
