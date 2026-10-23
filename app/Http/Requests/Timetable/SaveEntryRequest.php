<?php

namespace App\Http\Requests\Timetable;

use App\Models\TimetableEntry;

class SaveEntryRequest extends TimetableRequest
{
    public function authorize(): bool
    {
        $entry = $this->route('entry');

        return $entry instanceof TimetableEntry
            ? (bool) $this->user()?->can('update', $entry)
            : (bool) $this->user()?->can('create', TimetableEntry::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'term_id' => ['required', 'integer', $this->inSchool('terms')],
            'school_class_id' => ['required', 'integer', $this->inSchool('school_classes')],
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id'))],
            'subject_id' => ['required', 'integer', $this->inSchool('subjects')],
            'teacher_id' => ['nullable', 'integer', $this->inSchool('teachers')],
            'timetable_period_id' => ['required', 'integer', $this->inSchool('timetable_periods')],
            'weekday' => ['required', 'integer', 'between:1,7'],
            'room' => ['nullable', 'string', 'max:50'],
        ];
    }
}
