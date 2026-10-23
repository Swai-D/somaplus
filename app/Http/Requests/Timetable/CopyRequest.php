<?php

namespace App\Http\Requests\Timetable;

use App\Models\TimetableEntry;

class CopyRequest extends TimetableRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', TimetableEntry::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'from_term_id' => ['required', 'integer', $this->inSchool('terms')],
            'to_term_id' => ['required', 'integer', 'different:from_term_id', $this->inSchool('terms')],
        ];
    }
}
