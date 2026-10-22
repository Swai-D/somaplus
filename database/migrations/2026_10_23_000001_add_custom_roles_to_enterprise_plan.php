<?php

use App\Models\SubscriptionPlan;
use Illuminate\Database\Migrations\Migration;

/**
 * config/plans.php only seeds plans that do not exist yet, so the Enterprise plan that is already in
 * the table gets the `custom_roles` feature here. It only adds the feature; nothing is removed.
 */
return new class extends Migration
{
    public function up(): void
    {
        $plan = SubscriptionPlan::query()->where('key', 'enterprise')->first();

        if ($plan !== null && ! in_array('custom_roles', $plan->features, true)) {
            $plan->features = [...$plan->features, 'custom_roles'];
            $plan->save();
        }
    }

    public function down(): void
    {
        $plan = SubscriptionPlan::query()->where('key', 'enterprise')->first();

        if ($plan !== null) {
            $plan->features = array_values(array_diff($plan->features, ['custom_roles']));
            $plan->save();
        }
    }
};
