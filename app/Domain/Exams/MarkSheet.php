<?php

namespace App\Domain\Exams;

use App\Domain\Attendance\ClassRoll;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;

/**
 * The students of a class in an exam's academic year with their marks in one subject.
 * Grades are worked out here, from the exam's own grading scale, never taken from the client.
 */
final class MarkSheet
{
    public function __construct(private readonly ClassRoll $roll, private readonly GradeCalculator $grades) {}

    /**
     * @return list<MarkSheetRow>
     */
    public function rows(Exam $exam, SchoolClass $class, ?Stream $stream, Subject $subject): array
    {
        $students = $this->roll->students($class, $stream, $exam->academicYear()->firstOrFail());
        $scale = $exam->gradingScale()->with('bands')->firstOrFail();

        $marks = ExamMark::query()
            ->where('exam_id', $exam->id)
            ->where('subject_id', $subject->id)
            ->whereIn('student_id', $students->modelKeys())
            ->get()->keyBy('student_id');

        $rows = [];
        foreach ($students as $student) {
            $mark = $marks->get($student->id);
            $band = $mark !== null && $mark->score !== null
                ? $this->grades->grade($scale, Score::fromDecimal($mark->score), $exam->max_mark)
                : null;

            $rows[] = new MarkSheetRow($student, $mark, $band);
        }

        return $rows;
    }
}
