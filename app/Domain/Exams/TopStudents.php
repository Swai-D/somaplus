<?php

namespace App\Domain\Exams;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;

/**
 * The best students of the latest locked exam, worked out the same way the results page
 * works them out: the totals are whole hundredths, the average is cut (never rounded) and
 * the position comes from the exact averages, so equal averages share a position.
 *
 * The class of a student is the one stored with their marks (the snapshot), not where the
 * student is today, so a past exam gives the same list every time.
 */
final class TopStudents
{
    /**
     * @return array{exam: Exam|null, rows: list<array{student: Student, class: string, average: int, position: int|null}>}
     */
    public function handle(int $limit = 5): array
    {
        $exam = Exam::query()
            ->whereNotNull('locked_at')
            ->orderByDesc('locked_at')
            ->orderByDesc('id')
            ->first();

        if ($exam === null) {
            return ['exam' => null, 'rows' => []];
        }

        $marks = ExamMark::query()
            ->where('exam_id', $exam->id)
            ->whereNotNull('score')
            ->where('is_absent', false)
            ->orderBy('id')
            ->get(['student_id', 'school_class_id', 'score']);

        /** @var array<int, array{total: int, count: int, class: int}> $sums */
        $sums = [];
        foreach ($marks as $mark) {
            $sum = $sums[$mark->student_id] ?? ['total' => 0, 'count' => 0, 'class' => $mark->school_class_id];
            $sum['total'] += Score::fromDecimal($mark->score);
            $sum['count']++;
            $sums[$mark->student_id] = $sum;
        }

        if ($sums === []) {
            return ['exam' => $exam, 'rows' => []];
        }

        // A position is a rank inside the student's own class, so the classes are ranked apart.
        $byClass = [];
        foreach ($sums as $id => $sum) {
            $byClass[$sum['class']][$id] = ['total' => $sum['total'], 'count' => $sum['count']];
        }

        $positions = [];
        foreach ($byClass as $entries) {
            foreach (Positions::rank($entries) as $id => $position) {
                $positions[$id] = $position;
            }
        }

        $students = Student::query()->whereIn('id', array_keys($sums))->get()->keyBy('id');
        $classes = SchoolClass::query()->whereIn('id', array_keys($byClass))->get()->keyBy('id');

        $rows = [];
        foreach ($sums as $id => $sum) {
            $student = $students->get($id);
            if ($student === null) {
                continue;
            }

            $class = $classes->get($sum['class']);

            $rows[] = [
                'student' => $student,
                'class' => $class instanceof SchoolClass ? $class->name : '',
                'average' => intdiv($sum['total'], $sum['count']),
                'position' => $positions[$id] ?? null,
            ];
        }

        // The best average first; an equal average keeps the class position and then the name.
        usort($rows, fn (array $a, array $b): int => [$b['average'], $a['position'] ?? PHP_INT_MAX, $a['student']->first_name, $a['student']->last_name]
            <=> [$a['average'], $b['position'] ?? PHP_INT_MAX, $b['student']->first_name, $b['student']->last_name]);

        return ['exam' => $exam, 'rows' => array_slice($rows, 0, $limit)];
    }
}
