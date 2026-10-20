<?php

namespace App\Http\Requests\Exams;

use App\Models\Exam;

class ExamRequest extends ExamsRequest
{
    public function authorize(): bool
    {
        $exam = $this->route('exam');

        return $exam instanceof Exam
            ? (bool) $this->user()?->can('update', $exam)
            : (bool) $this->user()?->can('create', Exam::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $exam = $this->route('exam');

        return [
            'name' => ['required', 'string', 'max:100', $this->unique('exams', 'name', $exam instanceof Exam ? $exam->id : null)->where('term_id', $this->input('term_id'))],
            'term_id' => ['required', 'integer', $this->inSchool('terms')],
            'grading_scale_id' => ['required', 'integer', $this->inSchool('grading_scales')],
            'max_mark' => ['required', 'integer', 'between:1,1000'],
        ];
    }
}
