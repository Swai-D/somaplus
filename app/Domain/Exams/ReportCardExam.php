<?php

namespace App\Domain\Exams;

use App\Models\Exam;
use App\Models\SchoolClass;

/** The part of a report card for one locked exam: subject lines and the overall result with positions. */
final class ReportCardExam
{
    /**
     * @param  list<ReportCardLine>  $lines
     */
    public function __construct(
        public readonly Exam $exam,
        public readonly SchoolClass $class,
        public readonly array $lines,
        public readonly StudentResult $result,
    ) {}
}
