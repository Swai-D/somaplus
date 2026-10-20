<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

/**
 * An invoice is financial and personal data: looking needs `fees.view`, changing its control number `fees.manage`.
 */
class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('fees.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('fees.view');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('fees.manage');
    }
}
