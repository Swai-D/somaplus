<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'domain' => null,
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'subscription_status' => SubscriptionStatus::Active,
        ];
    }

    public function onPlan(SubscriptionPlan $plan): static
    {
        return $this->state(fn () => ['subscription_plan_id' => $plan->getKey()]);
    }

    public function status(SubscriptionStatus $status): static
    {
        return $this->state(fn () => ['subscription_status' => $status]);
    }
}
