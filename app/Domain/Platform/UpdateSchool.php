<?php

namespace App\Domain\Platform;

use App\Enums\SubscriptionStatus;
use App\Models\Tenant;

/**
 * Changes what a platform administrator may change about a school: its name, its own address, its plan and its
 * subscription state. The short name (slug) and the currency are fixed after creation (invoices and receipts are
 * held in the currency). Nothing is ever deleted: a lapsed school moves to grace, then read-only.
 */
final class UpdateSchool
{
    /**
     * @param  array{name: string, domain?: string|null, subscription_plan_id: int, subscription_status: string, grace_ends_at?: string|null}  $data
     */
    public function handle(Tenant $school, array $data): Tenant
    {
        $status = SubscriptionStatus::from($data['subscription_status']);

        $school->forceFill([
            'name' => $data['name'],
            'domain' => ($data['domain'] ?? null) ?: null,
            'subscription_plan_id' => $data['subscription_plan_id'],
            'subscription_status' => $status,
            'grace_ends_at' => $status === SubscriptionStatus::Grace ? ($data['grace_ends_at'] ?? null) : null,
        ])->save();

        return $school;
    }
}
