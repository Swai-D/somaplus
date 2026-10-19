<?php

namespace App\Http\Requests\Attendance;

use App\Models\AttendanceRecord;
use Illuminate\Validation\Rule;

class SaveAttendanceRequest extends AttendanceRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->classRules() + [
            'date' => ['required', 'date', 'before_or_equal:today'],
            'rows' => ['required', 'array', 'min:1', 'max:500'],
            'rows.*.status' => ['required', Rule::in(AttendanceRecord::STATUSES)],
            'rows.*.note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
