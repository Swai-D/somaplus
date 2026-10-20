<?php

namespace App\Domain\Exams;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;

/**
 * A student's report card for a term: every locked exam of the term in which the student has marks,
 * with the score and grade of each subject and the overall result and positions of that exam.
 * Exams that are not locked never appear, so a card only ever shows final marks. Nothing is stored:
 * the card is worked out again from the marks and the exam's own grading scale each time.
 */
final class ReportCard
{
    public function __construct(private readonly ResultSheet $sheet, private readonly GradeCalculator $grades) {}

    /**
     * @return list<ReportCardExam>
     */
    public function handle(Student $student, Term $term): array
    {
        $exams = Exam::query()->where('term_id', $term->id)->whereNotNull('locked_at')->with('gradingScale.bands')->orderBy('id')->get();

        $cards = [];
        foreach ($exams as $exam) {
            $marks = ExamMark::query()->where('exam_id', $exam->id)->where('student_id', $student->id)
                ->with('subject')->orderBy('id')->get();
            $last = $marks->last();
            if ($last === null) {
                continue;
            }

            $class = SchoolClass::query()->find($last->school_class_id);
            if ($class === null) {
                continue;
            }

            $result = collect($this->sheet->rows($exam, $class))->first(fn (StudentResult $r): bool => $r->student->id === $student->id);
            if ($result === null) {
                continue;
            }

            $lines = $marks->filter(fn (ExamMark $m): bool => $m->school_class_id === $class->id)
                ->sortBy(fn (ExamMark $m): string => $m->subject->name)
                ->map(function (ExamMark $m) use ($exam): ReportCardLine {
                    $score = $m->score !== null && ! $m->is_absent ? Score::fromDecimal($m->score) : null;

                    return new ReportCardLine(
                        $m->subject,
                        $score,
                        $m->is_absent,
                        $score !== null ? $this->grades->grade($exam->gradingScale, $score, $exam->max_mark) : null,
                    );
                })->values()->all();

            $cards[] = new ReportCardExam($exam, $class, $lines, $result);
        }

        return $cards;
    }
}
