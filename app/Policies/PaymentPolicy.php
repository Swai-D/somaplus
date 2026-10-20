<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

/**
 * Looking at payments needs `fees.view`, recording one `payments.record` and reversing one `payments.reverse`.
 * There is no update and no delete: a payment is never edited or removed.
 */
class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('fees.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->can('fees.view');
    }

    public function create(User $user): bool
    {
        return $user->can('payments.record');
    }

    public function reverse(User $user, Payment $payment): bool
    {
        return $user->can('payments.reverse');
    }
}
