<?php

namespace App\Http\Requests\Students;

use App\Models\Student;

class EnrollmentRequest extends StudentsRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');

        return $student instanceof Student && $this->user()?->can('update', $student);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->placementRules(true) + ['enrolled_on' => ['required', 'date']];
    }
}
