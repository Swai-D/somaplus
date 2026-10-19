<?php

namespace App\Http\Requests\Students;

use App\Models\Student;

class StudentRequest extends StudentsRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');

        return $student instanceof Student
            ? (bool) $this->user()?->can('update', $student)
            : (bool) $this->user()?->can('create', Student::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'admission_number' => ['required', 'string', 'max:30', $this->unique('students', 'admission_number', $student instanceof Student ? $student->id : null)],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'admitted_on' => ['required', 'date'],
        ] + ($student instanceof Student ? [] : $this->placementRules(false));
    }
}
