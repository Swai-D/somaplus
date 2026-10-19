<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * Unlinks a guardian from a student. A guardian left with no student is deleted,
 * because personal data nobody needs is not kept.
 */
final class RemoveGuardian
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Student $student, Guardian $guardian): void
    {
        DB::transaction(function () use ($student, $guardian): void {
            $student->guardians()->detach($guardian->id);

            if (! $guardian->students()->exists()) {
                $guardian->delete();
            }

            $this->audit->handle('guardian.unlinked', $student);
        });
    }
}
