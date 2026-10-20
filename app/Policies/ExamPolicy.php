<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

/**
 * Looking at exams and marks needs `exams.view`, changing exams (and locking them) `exams.manage`,
 * entering marks `marks.enter` and looking at results `results.view`. Permissions are checked inside the school's context.
 */
class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('exams.view');
    }

    public function view(User $user, Exam $exam): bool
    {
        return $user->can('exams.view');
    }

    public function create(User $user): bool
    {
        return $user->can('exams.manage');
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->can('exams.manage');
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $user->can('exams.manage');
    }

    public function enterMarks(User $user, Exam $exam): bool
    {
        return $user->can('marks.enter');
    }

    /** Results (totals, averages, positions) of an exam; the controller also needs the exam to be locked. */
    public function viewResults(User $user, Exam $exam): bool
    {
        return $user->can('results.view');
    }
}
