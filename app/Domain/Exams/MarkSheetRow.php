<?php

namespace App\Domain\Exams;

use App\Models\ExamMark;
use App\Models\GradingBand;
use App\Models\Student;

/**
 * One line of a mark sheet: a student, the stored mark (if any) and the grade worked out from it.
 */
final class MarkSheetRow
{
    public function __construct(
        public readonly Student $student,
        public readonly ?ExamMark $mark,
        public readonly ?GradingBand $band,
    ) {}

    public function score(): ?string
    {
        return $this->mark === null || $this->mark->score === null
            ? null
            : Score::format(Score::fromDecimal($this->mark->score));
    }

    public function isAbsent(): bool
    {
        return $this->mark !== null && $this->mark->is_absent;
    }
}
