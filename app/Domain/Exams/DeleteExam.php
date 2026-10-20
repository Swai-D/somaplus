<?php

namespace App\Domain\Exams;

use App\Domain\Settings\RecordAudit;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Deletes an exam that has no marks and is not locked. Marks are never silently removed.
 */
final class DeleteExam
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Exam $exam): void
    {
        DB::transaction(function () use ($exam): void {
            $fresh = Exam::query()->whereKey($exam->id)->lockForUpdate()->firstOrFail();

            if ($fresh->isLocked() || $fresh->marks()->exists()) {
                throw ValidationException::withMessages(['delete' => __('exams.exams.cannot_delete')]);
            }

            $this->audit->handle('exam.deleted', $fresh);
            $fresh->delete();
        });
    }
}
