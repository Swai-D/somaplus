<?php

namespace App\Http\Requests\Exams;

use App\Models\Exam;

/**
 * The class (required) and stream chosen on the results page (query string; no personal data).
 */
class ResultsQueryRequest extends ExamsRequest
{
    public function authorize(): bool
    {
        $exam = $this->route('exam');

        return $exam instanceof Exam && $this->user()?->can('viewResults', $exam);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'school_class_id' => ['nullable', 'integer', $this->inSchool('school_classes')],
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id'))],
        ];
    }
}
