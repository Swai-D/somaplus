<?php

namespace App\Http\Requests\Exams;

use App\Domain\Exams\TeachingAssignments;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;

class MarksRequest extends ExamsRequest
{
    public function authorize(): bool
    {
        $exam = $this->route('exam');

        if (! ($exam instanceof Exam) || ! $this->user()?->can('enterMarks', $exam)) {
            return false;
        }

        // A teacher writes only inside what the school assigned them; the owner and the
        // head teacher hold exams.manage and may enter marks for everything.
        $restrictions = app(TeachingAssignments::class)->restrictions($this->user());
        if ($restrictions === null) {
            return true;
        }

        // Rows that are not of the school, or missing ids, are left to the rules to name.
        $class = SchoolClass::query()->find((int) $this->input('school_class_id'));
        $subject = Subject::query()->find((int) $this->input('subject_id'));
        if ($class === null || $subject === null) {
            return true;
        }

        return $restrictions->classes()->whereKey($class->id)->exists()
            && $restrictions->subjects()->whereKey($subject->id)->exists();
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
