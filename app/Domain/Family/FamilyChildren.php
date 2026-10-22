<?php

namespace App\Domain\Family;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * The pupils a signed-in parent may see: those whose guardian record is linked to the parent's account. This is
 * the only place that decides it; the family pages ask here and answer 404 for anyone else's child.
 */
final class FamilyChildren
{
    /** @return Collection<int, Student> */
    public function for(User $user): Collection
    {
        return Student::query()
            ->whereIn('id', Guardian::query()->where('user_id', $user->id)->join('guardian_student', 'guardian_student.guardian_id', '=', 'guardians.id')->select('guardian_student.student_id'))
            ->orderBy('first_name')->orderBy('last_name')->get();
    }

    public function find(User $user, int $studentId): ?Student
    {
        return $this->for($user)->firstWhere('id', $studentId);
    }
}
