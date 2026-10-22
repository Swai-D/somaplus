<?php

namespace App\Policies;

use App\Models\BookIssue;
use App\Models\User;

/**
 * Who may see and change the issues of the library, that is, put a book out
 * and take it back. Reading needs `library.view`, changing `library.manage`.
 */
class BookIssuePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('library.view');
    }

    public function view(User $user, BookIssue $issue): bool
    {
        return $user->can('library.view');
    }

    public function create(User $user): bool
    {
        return $user->can('library.manage');
    }

    public function update(User $user, BookIssue $issue): bool
    {
        return $user->can('library.manage');
    }

    public function delete(User $user, BookIssue $issue): bool
    {
        return $user->can('library.manage');
    }
}
