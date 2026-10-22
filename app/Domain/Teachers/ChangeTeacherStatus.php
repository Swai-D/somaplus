<?php

namespace App\Domain\Teachers;

use App\Domain\Settings\RecordAudit;
use App\Models\Teacher;

/**
 * A teacher who leaves keeps the record and the marks they entered, so they are put
 * out of duty instead of being deleted: the status becomes inactive.
 */
final class ChangeTeacherStatus
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Teacher $teacher, string $status): Teacher
    {
        $teacher->status = $status === Teacher::INACTIVE ? Teacher::INACTIVE : Teacher::ACTIVE;
        $teacher->save();

        $this->audit->handle('teacher.status_changed', $teacher);

        return $teacher;
    }
}
