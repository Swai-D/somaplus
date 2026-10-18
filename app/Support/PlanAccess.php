<?php

namespace App\Support;

use App\Models\SubscriptionPlan;

/**
 * What the current school's plan allows. With no plan (no tenant), nothing is allowed.
 */
final readonly class PlanAccess
{
    public function __construct(private ?SubscriptionPlan $plan = null) {}

    public function allows(string $feature): bool
    {
        return $this->plan?->allows($feature) ?? false;
    }

    public function key(): ?string
    {
        return $this->plan?->key;
    }
}
