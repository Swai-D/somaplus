<?php

namespace App\Http\Requests\Students;

use App\Models\Guardian;
use App\Models\Student;

/**
 * Links a pupil to a guardian of the school: which pupil, and how they are related. The pupil
 * must be a pupil of this school.
 */
class GuardianStudentRequest extends StudentsRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Guardian::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', $this->inSchool('students')],
            'relationship' => ['required', 'in:father,mother,guardian,other'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }

    /** The pupil the guardian is linked to, or null when the choice is not valid. */
    public function student(): ?Student
    {
        $id = (int) $this->validated('student_id');

        return Student::query()->whereKey($id)->first();
    }
}
