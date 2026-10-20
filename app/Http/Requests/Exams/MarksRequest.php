<?php

namespace App\Http\Requests\Exams;

use App\Models\Exam;

class MarksRequest extends ExamsRequest
{
    public function authorize(): bool
    {
        $exam = $this->route('exam');

        return $exam instanceof Exam && $this->user()?->can('enterMarks', $exam);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'school_class_id' => ['required', 'integer', $this->inSchool('school_classes')],
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id'))],
            'subject_id' => ['required', 'integer', $this->inSchool('subjects')],
            'rows' => ['required', 'array', 'min:1', 'max:500'],
            'rows.*.score' => ['nullable', 'string', 'max:10'],
            'rows.*.absent' => ['nullable', 'boolean'],
        ];
    }
}
