<?php

namespace App\Http\Requests\Timetable;

use App\Models\TimetableEntry;

/** The filters of the timetable pages: a term, a class and stream, or a teacher. */
class ViewTimetableRequest extends TimetableRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('viewAny', TimetableEntry::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'term_id' => ['nullable', 'integer', $this->inSchool('terms')],
            'school_class_id' => ['nullable', 'integer', $this->inSchool('school_classes')],
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id'))],
            'teacher_id' => ['nullable', 'integer', $this->inSchool('teachers')],
        ];
    }
}
