<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

/**
 * Who may see and change the books of the library. Reading needs `library.view`
 * and changing needs `library.manage`, like the rest of the section.
 */
class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('library.view');
    }

    public function view(User $user, Book $book): bool
    {
        return $user->can('library.view');
    }

    public function create(User $user): bool
    {
        return $user->can('library.manage');
    }

    public function update(User $user, Book $book): bool
    {
        return $user->can('library.manage');
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->can('library.manage');
    }
}
