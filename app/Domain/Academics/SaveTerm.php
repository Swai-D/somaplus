<?php

namespace App\Domain\Academics;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Validation\ValidationException;

/**
 * Creates or changes a term of an academic year. A term lies inside its year and
 * terms of one year never overlap.
 */
final class SaveTerm
{
    /**
     * @param  array{name: string, starts_on: string, ends_on: string}  $data
     */
    public function handle(AcademicYear $year, array $data, ?Term $term = null): Term
    {
        $starts = $data['starts_on'];
        $ends = $data['ends_on'];

        if ($starts < $year->starts_on->toDateString() || $ends > $year->ends_on->toDateString()) {
            throw ValidationException::withMessages(['starts_on' => __('academics.terms.outside_year', [
                'from' => $year->starts_on->toFormattedDateString(),
                'to' => $year->ends_on->toFormattedDateString(),
            ])]);
        }

        $overlaps = $year->terms()
            ->when($term !== null, fn ($query) => $query->whereKeyNot($term?->getKey()))
            ->whereDate('starts_on', '<=', $ends)
            ->whereDate('ends_on', '>=', $starts)
            ->exists();

        if ($overlaps) {
            throw ValidationException::withMessages(['starts_on' => __('academics.terms.overlap')]);
        }

        $term ??= new Term;
        $term->academic_year_id = $year->id;
        $term->fill(['name' => $data['name'], 'starts_on' => $starts, 'ends_on' => $ends])->save();

        return $term;
    }
}
