<?php

namespace App\Domain\Timetable;

use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Puts one lesson on the timetable, or changes it. This is where a timetable stays honest: inside one
 * transaction that first locks the period (so two people booking the same slot take turns) it refuses
 *
 *  - a break as a lesson slot,
 *  - a class or stream that already has a lesson then (a lesson for the whole class clashes with every
 *    stream, and a stream's lesson clashes with the whole class's),
 *  - a teacher who already teaches elsewhere then,
 *  - a room that is already used then,
 *  - a teacher who is out of duty, or who is given a subject or a class outside what the school assigned
 *    them (a teacher with nothing assigned yet is not restricted).
 *
 * Ids of other schools never get here: the form checks them against the school, and every query below
 * is scoped to it.
 */
final class SaveTimetableEntry
{
    /**
     * @param  array{term_id: int, school_class_id: int, stream_id?: int|null, subject_id: int, teacher_id?: int|null, timetable_period_id: int, weekday: int, room?: string|null}  $data
     */
    public function handle(array $data, ?TimetableEntry $entry = null): TimetableEntry
    {
        return DB::transaction(function () use ($data, $entry): TimetableEntry {
            $period = TimetablePeriod::query()->lockForUpdate()->findOrFail($data['timetable_period_id']);

            if ($period->is_break) {
                throw ValidationException::withMessages(['timetable_period_id' => __('timetable.errors.is_break')]);
            }

            $streamId = $data['stream_id'] ?? null;
            $teacherId = $data['teacher_id'] ?? null;
            $room = trim((string) ($data['room'] ?? ''));
            $room = $room === '' ? null : $room;

            $slot = TimetableEntry::query()
                ->where('term_id', $data['term_id'])
                ->where('weekday', $data['weekday'])
                ->where('timetable_period_id', $period->id)
                ->when($entry !== null, fn ($q) => $q->whereKeyNot($entry?->id));

            $classBusy = (clone $slot)
                ->where('school_class_id', $data['school_class_id'])
                ->when($streamId !== null, fn ($q) => $q->where(fn ($w) => $w->whereNull('stream_id')->orWhere('stream_id', $streamId)))
                ->exists();

            if ($classBusy) {
                throw ValidationException::withMessages(['timetable_period_id' => __('timetable.errors.class_busy')]);
            }

            if ($teacherId !== null) {
                $this->checkTeacher($teacherId, $data['subject_id'], $data['school_class_id']);

                if ((clone $slot)->where('teacher_id', $teacherId)->exists()) {
                    throw ValidationException::withMessages(['teacher_id' => __('timetable.errors.teacher_busy')]);
                }
            }

            if ($room !== null && (clone $slot)->whereRaw('lower(room) = ?', [mb_strtolower($room)])->exists()) {
                throw ValidationException::withMessages(['room' => __('timetable.errors.room_busy')]);
            }

            $entry ??= new TimetableEntry(['created_by' => Auth::id()]);
            $entry->fill([
                'term_id' => $data['term_id'],
                'school_class_id' => $data['school_class_id'],
                'stream_id' => $streamId,
                'subject_id' => $data['subject_id'],
                'teacher_id' => $teacherId,
                'timetable_period_id' => $period->id,
                'weekday' => $data['weekday'],
                'room' => $room,
            ])->save();

            return $entry;
        });
    }

    private function checkTeacher(int $teacherId, int $subjectId, int $classId): void
    {
        $teacher = Teacher::query()->find($teacherId);

        if ($teacher === null) {
            throw ValidationException::withMessages(['teacher_id' => __('timetable.errors.teacher_unknown')]);
        }

        if (! $teacher->isActive()) {
            throw ValidationException::withMessages(['teacher_id' => __('timetable.errors.teacher_inactive')]);
        }

        if ($teacher->subjects()->exists() && ! $teacher->subjects()->whereKey($subjectId)->exists()) {
            throw ValidationException::withMessages(['teacher_id' => __('timetable.errors.teacher_subject')]);
        }

        if ($teacher->classes()->exists() && ! $teacher->classes()->whereKey($classId)->exists()) {
            throw ValidationException::withMessages(['teacher_id' => __('timetable.errors.teacher_class')]);
        }
    }
}
