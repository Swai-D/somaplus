<?php

namespace App\Domain\Exams;

use App\Domain\Settings\RecordAudit;
use App\Models\GradingScale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Deletes a scale that no exam uses. A used scale stays, because past grades depend on it.
 */
final class DeleteGradingScale
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(GradingScale $scale): void
    {
        DB::transaction(function () use ($scale): void {
            GradingScale::query()->whereKey($scale->id)->lockForUpdate()->firstOrFail();

            if ($scale->isUsed()) {
                throw ValidationException::withMessages(['delete' => __('exams.scales.used')]);
            }

            $this->audit->handle('grading_scale.deleted', $scale);
            $scale->bands()->delete();
            $scale->delete();
        });
    }
}
