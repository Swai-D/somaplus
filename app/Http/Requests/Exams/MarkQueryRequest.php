<?php

namespace App\Http\Requests\Exams;

use App\Models\Exam;

/**
 * The class, stream and subject chosen on the mark sheet page (query string; no personal data).
 */
class MarkQueryRequest extends ExamsRequest
{
    public function authorize(): bool
    {
        $exam = $this->route('exam');

        return $exam instanceof Exam && $this->user()?->can('view', $exam);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'school_class_id' => ['nullable', 'integer', $this->inSchool('school_classes')],
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id'))],
            'subject_id' => ['nullable', 'integer', $this->inSchool('subjects')],
        ];
    }
}
