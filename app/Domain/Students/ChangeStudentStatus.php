<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\Student;

/**
 * A student who leaves is marked as left, with the date and a reason; the record
 * and its history stay. Reinstating makes the student active again.
 */
final class ChangeStudentStatus
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function leave(Student $student, string $leftOn, ?string $reason): void
    {
        $reason = trim((string) $reason);

        $student->forceFill([
            'status' => Student::LEFT,
            'left_on' => $leftOn,
            'left_reason' => $reason === '' ? null : $reason,
        ])->save();

        $this->audit->handle('student.left', $student);
    }

    public function reinstate(Student $student): void
    {
        $student->forceFill(['status' => Student::ACTIVE, 'left_on' => null, 'left_reason' => null])->save();

        $this->audit->handle('student.reinstated', $student);
    }
}
