<?php

namespace App\Http\Requests\Students;

use App\Models\Student;

class LeaveRequest extends StudentsRequest
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
        return [
            'left_on' => ['required', 'date'],
            'left_reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
