<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'name' => fake()->unique()->words(2, true),
            'features' => [],
            'sort_order' => 0,
        ];
    }

    /**
     * @param  array<int, string>  $features
     */
    public function withFeatures(array $features): static
    {
        return $this->state(fn () => ['features' => $features]);
    }
}
