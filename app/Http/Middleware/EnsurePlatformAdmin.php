<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware `platform.admin`: the pages that manage all schools exist only for a platform administrator on
 * a central address. Anyone else (including a school's owner, or a school address) gets a plain 404, so the area
 * is not revealed.
 */
class EnsurePlatformAdmin
{
    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->isSuperAdmin() && $this->context->currentId() === null, 404);

        return $next($request);
    }
}
