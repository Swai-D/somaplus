<?php

namespace App\Domain\Exams;

use App\Domain\Attendance\ClassRoll;
use App\Domain\Settings\RecordAudit;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Saves the marks of one class (and stream) in one subject of one exam.
 *
 * Everything is checked on the server and nothing is saved unless every row is valid: the exam must not
 * be locked, each student must be in that class for the exam's academic year, a score must be a number
 * with at most two decimals between 0 and the exam's maximum, and a student cannot be both absent and
 * scored. An empty score leaves the stored mark as it is. There is one row per exam, subject and student,
 * so sending the same sheet again, or correcting it, updates rows and never adds duplicates.
 */
final class RecordMarks
{
    public function __construct(private readonly ClassRoll $roll, private readonly RecordAudit $audit) {}

    /**
     * @param  array<int|string, array{score?: mixed, absent?: mixed}>  $rows  keyed by student id
     */
    public function handle(Exam $exam, SchoolClass $class, ?Stream $stream, Subject $subject, array $rows): int
    {
        if ($exam->isLocked()) {
            throw ValidationException::withMessages(['exam' => __('exams.errors.locked')]);
        }

        $year = $exam->academicYear()->firstOrFail();
        $students = $this->roll->students($class, $stream, $year)->keyBy('id');
        $maxHundredths = $exam->max_mark * 100;

        $errors = [];
        $valid = [];

        foreach ($rows as $key => $row) {
            $studentId = ctype_digit((string) $key) ? (int) $key : 0;

            if ($students->get($studentId) === null) {
                $errors['rows'] = __('exams.errors.not_in_class');

                continue;
            }

            $absent = filter_var($row['absent'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $typed = trim((string) ($row['score'] ?? ''));

            if ($absent && $typed !== '') {
                $errors["rows.{$studentId}.score"] = __('exams.errors.absent_and_score');
            } elseif ($absent) {
                $valid[$studentId] = null;
            } elseif ($typed !== '') {
                $score = Score::parse($typed);

                if ($score === null) {
                    $errors["rows.{$studentId}.score"] = __('exams.errors.bad_score');
                } elseif ($score > $maxHundredths) {
                    $errors["rows.{$studentId}.score"] = __('exams.errors.above_max', ['max' => $exam->max_mark]);
                } else {
                    $valid[$studentId] = $score;
                }
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        try {
            return $this->save($exam, $class, $stream, $subject, $valid);
        } catch (UniqueConstraintViolationException) {
            // Two requests created the same mark at the same moment: the second one now updates it.
            return $this->save($exam, $class, $stream, $subject, $valid);
        }
    }

    /**
     * @param  array<int, int|null>  $valid  student id => score in hundredths, null for absent
     */
    private function save(Exam $exam, SchoolClass $class, ?Stream $stream, Subject $subject, array $valid): int
    {
        return DB::transaction(function () use ($exam, $class, $stream, $subject, $valid): int {
            // The lock is checked again under a row lock, so a lock made a moment ago cannot be missed.
            $fresh = Exam::query()->whereKey($exam->id)->lockForUpdate()->firstOrFail();
            if ($fresh->isLocked()) {
                throw ValidationException::withMessages(['exam' => __('exams.errors.locked')]);
            }

            foreach ($valid as $studentId => $score) {
                $mark = ExamMark::query()
                    ->where('exam_id', $exam->id)
                    ->where('subject_id', $subject->id)
                    ->where('student_id', $studentId)
                    ->first() ?? new ExamMark;

                $mark->exam_id = $exam->id;
                $mark->fill([
                    'subject_id' => $subject->id,
                    'student_id' => $studentId,
                    'school_class_id' => $class->id,
                    'stream_id' => $stream?->id,
                    'score' => $score === null ? null : Score::toDecimal($score),
                    'is_absent' => $score === null,
                    'entered_by' => Auth::id(),
                ])->save();
            }

            $this->audit->handle('marks.recorded', $exam);

            return count($valid);
        });
    }
}
