<?php

namespace App\Domain\Timetable;

use App\Models\TimetablePeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Saves a lesson slot (or a break) of the school's day. Slots of one school never overlap, and a slot
 * that already holds lessons cannot be turned into a break.
 */
final class SaveTimetablePeriod
{
    /**
     * @param  array{name: string, starts_at: string, ends_at: string, is_break?: bool}  $data  times as HH:MM
     */
    public function handle(array $data, ?TimetablePeriod $period = null): TimetablePeriod
    {
        $start = substr($data['starts_at'], 0, 5).':00';
        $end = substr($data['ends_at'], 0, 5).':00';
        $isBreak = (bool) ($data['is_break'] ?? false);

        if ($end <= $start) {
            throw ValidationException::withMessages(['ends_at' => __('timetable.errors.period_order')]);
        }

        return DB::transaction(function () use ($data, $period, $start, $end, $isBreak): TimetablePeriod {
            $overlaps = TimetablePeriod::query()
                ->when($period !== null, fn ($q) => $q->whereKeyNot($period?->id))
                ->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start)
                ->exists();

            if ($overlaps) {
                throw ValidationException::withMessages(['starts_at' => __('timetable.errors.period_overlap')]);
            }

            if ($period !== null && $isBreak && $period->entries()->exists()) {
                throw ValidationException::withMessages(['is_break' => __('timetable.errors.period_has_lessons')]);
            }

            $period ??= new TimetablePeriod;
            $period->fill(['name' => trim($data['name']), 'starts_at' => $start, 'ends_at' => $end, 'is_break' => $isBreak])->save();

            return $period;
        });
    }
}
