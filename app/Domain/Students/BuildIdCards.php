<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Support\Code128;
use Illuminate\Support\Collection;

/**
 * Gathers what is printed on each student's ID card: name, admission number, class and stream of the
 * current academic year, the year, and a barcode of the admission number. Only what a card needs is used:
 * no date of birth, no parent details, no photo (Somaplus does not collect photos of children).
 * Each card printed leaves one audit line with the student's id.
 */
final class BuildIdCards
{
    public function __construct(private readonly CurrentPlacements $placements, private readonly RecordAudit $audit) {}

    /**
     * @param  Collection<int, Student>  $students  active students of this school
     * @return list<array{name: string, initials: string, number: string, class: string|null, year: string|null, barcode: string|null}>
     */
    public function handle(Collection $students): array
    {
        $year = AcademicYear::query()->where('is_current', true)->first();
        $placements = $this->placements->for($students, $year);
        $cards = [];

        foreach ($students as $student) {
            $placement = $placements[$student->id] ?? null;
            $class = $placement?->schoolClass->name;
            if ($class !== null && $placement->stream !== null) {
                $class .= ' '.$placement->stream->name;
            }

            $cards[] = [
                'name' => $student->fullName(),
                'initials' => mb_strtoupper(mb_substr($student->first_name, 0, 1).mb_substr($student->last_name, 0, 1)),
                'number' => $student->admission_number,
                'class' => $class,
                'year' => $year?->name,
                'barcode' => Code128::svg($student->admission_number, 36),
            ];

            $this->audit->handle('student.id_card_printed', $student);
        }

        return $cards;
    }
}
