<?php

namespace App\Domain\Exams;

use App\Models\GradingBand;
use App\Models\Subject;

/** One subject line of a report card: the score, or absent, and the grade worked out from it. */
final class ReportCardLine
{
    public function __construct(
        public readonly Subject $subject,
        public readonly ?int $score,
        public readonly bool $absent,
        public readonly ?GradingBand $band,
    ) {}

    public function scoreText(): ?string
    {
        return $this->score === null ? null : Score::format($this->score);
    }
}
