<?php

namespace App\Domain\Exams;

use App\Domain\Settings\RecordAudit;
use App\Models\Exam;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Locks an exam so no mark can be entered or changed, and unlocks it again (both are audited).
 */
final class LockExam
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function lock(Exam $exam): void
    {
        DB::transaction(function () use ($exam): void {
            $fresh = Exam::query()->whereKey($exam->id)->lockForUpdate()->firstOrFail();

            if (! $fresh->isLocked()) {
                $fresh->locked_at = CarbonImmutable::now();
                $fresh->locked_by = Auth::id();
                $fresh->save();
                $this->audit->handle('exam.locked', $fresh);
            }
        });
    }

    public function unlock(Exam $exam): void
    {
        DB::transaction(function () use ($exam): void {
            $fresh = Exam::query()->whereKey($exam->id)->lockForUpdate()->firstOrFail();

            if ($fresh->isLocked()) {
                $fresh->locked_at = null;
                $fresh->locked_by = null;
                $fresh->save();
                $this->audit->handle('exam.unlocked', $fresh);
            }
        });
    }
}
