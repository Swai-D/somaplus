<?php

namespace App\Policies;

use App\Models\FeeStructure;
use App\Models\User;

/**
 * Looking at fees needs `fees.view`; changing structures and making invoices needs `fees.manage`.
 */
class FeeStructurePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('fees.view');
    }

    public function view(User $user, FeeStructure $structure): bool
    {
        return $user->can('fees.view');
    }

    public function create(User $user): bool
    {
        return $user->can('fees.manage');
    }

    public function update(User $user, FeeStructure $structure): bool
    {
        return $user->can('fees.manage');
    }

    public function delete(User $user, FeeStructure $structure): bool
    {
        return $user->can('fees.manage');
    }

    public function generate(User $user, FeeStructure $structure): bool
    {
        return $user->can('fees.manage');
    }
}
