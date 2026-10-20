<?php

namespace App\Domain\Exams;

use App\Models\GradingBand;
use App\Models\Student;

/**
 * One student's result in one exam: total and average over the subjects with a score, the grade of the
 * average and the positions. Without a single score there is no average, grade or position.
 */
final class StudentResult
{
    public function __construct(
        public readonly Student $student,
        public readonly ?int $streamId,
        public readonly int $total,
        public readonly int $subjects,
        public readonly int $absent,
        public readonly ?GradingBand $band,
        public readonly ?int $classPosition,
        public readonly ?int $streamPosition,
        public readonly int $classSize,
        public readonly int $streamSize,
    ) {}

    public function hasScores(): bool
    {
        return $this->subjects > 0;
    }

    /** The average mark, in hundredths, cut (not rounded) so it never shows more than the real average. */
    public function average(): ?int
    {
        return $this->subjects > 0 ? intdiv($this->total, $this->subjects) : null;
    }

    public function totalText(): string
    {
        return Score::format($this->total);
    }

    public function averageText(): ?string
    {
        $average = $this->average();

        return $average === null ? null : Score::format($average);
    }
}
