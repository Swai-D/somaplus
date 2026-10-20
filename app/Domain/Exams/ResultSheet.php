<?php

namespace App\Domain\Exams;

use App\Domain\Attendance\ClassRoll;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use LogicException;

/**
 * The results of a class in a locked exam, worked out on the server from the stored marks and the exam's
 * own grading scale, so a past term gives the same result every time.
 *
 * - The class is the one recorded with each mark (the snapshot), not where the student is today.
 * - Total = sum of the scores; absent subjects and empty scores are not counted and add nothing.
 * - Average = total / number of scored subjects, graded exactly (never rounded up into a better grade).
 * - Position = rank by exact average among the students of the class with at least one score;
 *   equal averages tie. The stream position is the rank among the students of the same stream.
 * - Students placed in the class who have no mark are listed last without average or position.
 */
final class ResultSheet
{
    public function __construct(private readonly ClassRoll $roll, private readonly GradeCalculator $grades) {}

    /**
     * @return list<StudentResult> ranked first (by class position, then name), students without scores last
     */
    public function rows(Exam $exam, SchoolClass $class, ?Stream $stream = null): array
    {
        if (! $exam->isLocked()) {
            throw new LogicException('Results are only worked out for a locked exam.');
        }

        $scale = $exam->gradingScale()->with('bands')->firstOrFail();

        /** @var array<int, array{total: int, subjects: int, absent: int, stream: int|null}> $sums */
        $sums = [];
        $marks = ExamMark::query()->where('exam_id', $exam->id)->where('school_class_id', $class->id)->orderBy('id')->get();
        foreach ($marks as $mark) {
            $sum = $sums[$mark->student_id] ?? ['total' => 0, 'subjects' => 0, 'absent' => 0, 'stream' => null];
            if ($mark->score !== null && ! $mark->is_absent) {
                $sum['total'] += Score::fromDecimal($mark->score);
                $sum['subjects']++;
            } elseif ($mark->is_absent) {
                $sum['absent']++;
            }
            $sum['stream'] = $mark->stream_id;
            $sums[$mark->student_id] = $sum;
        }

        $students = Student::query()->whereIn('id', array_keys($sums))->get()->keyBy('id');
        foreach ($this->roll->students($class, null, $exam->academicYear()->firstOrFail()) as $student) {
            if (! isset($sums[$student->id])) {
                $students->put($student->id, $student);
                $sums[$student->id] = ['total' => 0, 'subjects' => 0, 'absent' => 0, 'stream' => null];
            }
        }

        $entries = array_map(fn (array $s): array => ['total' => $s['total'], 'count' => $s['subjects']], $sums);
        $classPositions = Positions::rank($entries);
        $classSize = count($classPositions);

        $byStream = [];
        foreach ($sums as $id => $sum) {
            if ($sum['stream'] !== null && $sum['subjects'] > 0) {
                $byStream[$sum['stream']][$id] = $entries[$id];
            }
        }
        $streamPositions = array_map(fn (array $group): array => Positions::rank($group), $byStream);

        $results = [];
        foreach ($sums as $id => $sum) {
            $student = $students->get($id);
            if ($student === null || ($stream !== null && $sum['stream'] !== $stream->id)) {
                continue;
            }

            $results[] = new StudentResult(
                $student,
                $sum['stream'],
                $sum['total'],
                $sum['subjects'],
                $sum['absent'],
                $sum['subjects'] > 0 ? $this->grades->gradeAverage($scale, $sum['total'], $sum['subjects'], $exam->max_mark) : null,
                $classPositions[$id] ?? null,
                $sum['stream'] !== null ? ($streamPositions[$sum['stream']][$id] ?? null) : null,
                $classSize,
                $sum['stream'] !== null ? count($streamPositions[$sum['stream']] ?? []) : 0,
            );
        }

        usort($results, fn (StudentResult $a, StudentResult $b): int => [$a->classPosition ?? PHP_INT_MAX, $a->student->first_name, $a->student->last_name, $a->student->id]
            <=> [$b->classPosition ?? PHP_INT_MAX, $b->student->first_name, $b->student->last_name, $b->student->id]);

        return $results;
    }
}
