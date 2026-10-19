<?php

namespace App\Http\Requests\Attendance;

/**
 * The class, stream and dates chosen on the roll and summary pages (query string; no personal data).
 */
class ViewAttendanceRequest extends AttendanceRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'school_class_id' => ['nullable', 'integer', $this->inSchool('school_classes')],
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id'))],
            'date' => ['nullable', 'date', 'before_or_equal:today'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ];
    }
}
