<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\User;

/**
 * A receipt is financial and personal data: looking at it needs `fees.view`.
 */
class ReceiptPolicy
{
    public function view(User $user, Receipt $receipt): bool
    {
        return $user->can('fees.view');
    }
}
