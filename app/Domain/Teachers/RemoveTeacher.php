<?php

namespace App\Domain\Teachers;

use App\Domain\Settings\RecordAudit;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

/**
 * Takes a teacher out of the school. The record of the teacher and what it teaches are
 * gone, but the account stays: the marks and the attendance the person entered keep
 * their name, and the school can give the person another role (Settings > Users).
 */
final class RemoveTeacher
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Teacher $teacher): void
    {
        DB::transaction(function () use ($teacher): void {
            $this->audit->handle('teacher.deleted', $teacher);

            $teacher->subjects()->detach();
            $teacher->classes()->detach();
            $teacher->delete();

            $teacher->user?->syncRoles([]);
        });
    }
}
