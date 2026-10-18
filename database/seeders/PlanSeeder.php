<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

/**
 * Creates the plans listed in config/plans.php that do not exist yet.
 *
 * It never overwrites an existing plan: the table is the source of truth, and
 * changing a plan's features needs a data migration.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('plans.plans', []) as $key => $plan) {
            SubscriptionPlan::query()->firstOrCreate(
                ['key' => $key],
                [
                    'name' => $plan['name'],
                    'features' => $plan['features'],
                    'sort_order' => $plan['sort_order'],
                ],
            );
        }
    }
}
