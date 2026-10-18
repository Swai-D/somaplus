<?php

namespace App\Http\Middleware;

use App\Support\Plans;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware `plan:<feature>[,<feature>...]`: the current school's plan
 * must include every listed feature.
 */
class EnsurePlanAllows
{
    public function handle(Request $request, Closure $next, string ...$features): Response
    {
        $plan = Plans::current();

        foreach ($features as $feature) {
            abort_unless($plan->allows($feature), 403, __('tenancy.plan_required'));
        }

        return $next($request);
    }
}
