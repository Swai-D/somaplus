<?php

namespace App\Policies;

use App\Models\LibraryMember;
use App\Models\User;

/**
 * Who may see and change the members of the library. Reading needs `library.view`
 * and changing needs `library.manage`, like the rest of the section.
 */
class LibraryMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('library.view');
    }

    public function view(User $user, LibraryMember $member): bool
    {
        return $user->can('library.view');
    }

    public function create(User $user): bool
    {
        return $user->can('library.manage');
    }

    public function update(User $user, LibraryMember $member): bool
    {
        return $user->can('library.manage');
    }

    public function delete(User $user, LibraryMember $member): bool
    {
        return $user->can('library.manage');
    }
}
