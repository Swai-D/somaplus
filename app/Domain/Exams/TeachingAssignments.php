<?php

namespace App\Domain\Exams;

use App\Models\Teacher;
use App\Models\User;

/**
 * What a teacher may enter marks for. A school keeps what each teacher teaches in
 * `teacher_subject` and `teacher_class`, so a person whose only say in marks is the
 * teacher's own `marks.enter` may write inside those two lists and nowhere else.
 * The owner and the head teacher hold `exams.manage` and are never restricted.
 */
final class TeachingAssignments
{
    /**
     * The teacher record when the person may only enter marks inside what the school
     * assigned them, null when nothing restricts them.
     */
    public function restrictions(User $user): ?Teacher
    {
        if ($user->can('exams.manage')) {
            return null;
        }

        return Teacher::query()->where('user_id', $user->id)->first();
    }

    /** The school assigned this class to the person. */
    public function teachesClass(?Teacher $restrictions, int $classId): bool
    {
        return $restrictions === null || $restrictions->classes()->whereKey($classId)->exists();
    }

    /** The school assigned this subject to the person. */
    public function teachesSubject(?Teacher $restrictions, int $subjectId): bool
    {
        return $restrictions === null || $restrictions->subjects()->whereKey($subjectId)->exists();
    }
}
