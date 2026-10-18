<?php

namespace App\Domain\Academics;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates or changes an academic year of the current school. Years of one school
 * never overlap, a year's terms always stay inside its dates, and at most one
 * year is current: marking a year current un-marks the previous one.
 */
final class SaveAcademicYear
{
    /**
     * @param  array{name: string, starts_on: string, ends_on: string, is_current?: bool}  $data
     */
    public function handle(array $data, ?AcademicYear $year = null): AcademicYear
    {
        return DB::transaction(function () use ($data, $year): AcademicYear {
            $starts = $data['starts_on'];
            $ends = $data['ends_on'];

            $overlaps = AcademicYear::query()
                ->when($year !== null, fn ($query) => $query->whereKeyNot($year?->getKey()))
                ->whereDate('starts_on', '<=', $ends)
                ->whereDate('ends_on', '>=', $starts)
                ->exists();

            if ($overlaps) {
                throw ValidationException::withMessages(['starts_on' => __('academics.years.overlap')]);
            }

            if ($year !== null) {
                $termsOutside = $year->terms()
                    ->where(fn ($query) => $query->whereDate('starts_on', '<', $starts)->orWhereDate('ends_on', '>', $ends))
                    ->exists();

                if ($termsOutside) {
                    throw ValidationException::withMessages(['starts_on' => __('academics.years.terms_outside')]);
                }
            }

            $year ??= new AcademicYear;
            $current = (bool) ($data['is_current'] ?? false);

            if ($current) {
                AcademicYear::query()
                    ->where('is_current', true)
                    ->when($year->exists, fn ($query) => $query->whereKeyNot($year->getKey()))
                    ->update(['is_current' => false]);
            }

            $year->fill([
                'name' => $data['name'],
                'starts_on' => $starts,
                'ends_on' => $ends,
                'is_current' => $current,
            ])->save();

            return $year;
        });
    }
}
