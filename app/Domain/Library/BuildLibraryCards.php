<?php

namespace App\Domain\Library;

use App\Domain\Settings\RecordAudit;
use App\Domain\Students\CurrentPlacements;
use App\Models\AcademicYear;
use App\Models\LibraryMember;
use App\Support\Code128;
use Illuminate\Support\Collection;

/**
 * Gathers what is printed on a member's library card: the name of the pupil, the card
 * number of the library, the class and stream of the current academic year, the year,
 * and a barcode of the card number. Only what a card needs is used: no guardian phone,
 * no address. Each card printed leaves one audit line with the member's id.
 */
final class BuildLibraryCards
{
    public function __construct(private readonly CurrentPlacements $placements, private readonly RecordAudit $audit) {}

    /**
     * @param  Collection<int, LibraryMember>  $members  members of this school
     * @return list<array{name: string, initials: string, number: string, class: string|null, year: string|null, barcode: string|null}>
     */
    public function handle(Collection $members): array
    {
        $year = AcademicYear::query()->where('is_current', true)->first();
        $placements = $this->placements->for($members->map(fn (LibraryMember $member) => $member->student), $year);
        $cards = [];

        foreach ($members as $member) {
            $placement = $placements[$member->student_id] ?? null;
            $class = $placement?->schoolClass->name;
            if ($class !== null && $placement->stream !== null) {
                $class .= ' '.$placement->stream->name;
            }

            $cards[] = [
                'name' => $member->fullName(),
                'initials' => mb_strtoupper(mb_substr($member->student->first_name, 0, 1).mb_substr($member->student->last_name, 0, 1)),
                'number' => $member->card_number,
                'class' => $class,
                'year' => $year?->name,
                'barcode' => Code128::svg($member->card_number, 36),
            ];

            $this->audit->handle('library.card_printed', $member);
        }

        return $cards;
    }
}
