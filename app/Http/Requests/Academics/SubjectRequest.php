<?php

namespace App\Http\Requests\Academics;

use App\Models\Subject;

class SubjectRequest extends AcademicsRequest
{
    public function authorize(): bool
    {
        $subject = $this->route('subject');

        return $subject instanceof Subject
            ? (bool) $this->user()?->can('update', $subject)
            : (bool) $this->user()?->can('create', Subject::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $subject = $this->route('subject');
        $ignore = $subject instanceof Subject ? $subject->id : null;

        return [
            'name' => ['required', 'string', 'max:100', $this->unique('subjects', 'name', $ignore)],
            'code' => ['nullable', 'string', 'max:20', $this->unique('subjects', 'code', $ignore)],
        ];
    }
}
