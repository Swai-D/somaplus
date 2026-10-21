<?php

namespace App\Http\Requests\Students;

use App\Models\Student;

class GuardianRequest extends StudentsRequest
{
    /** A phone number as the guardian form and the bulk import accept it. */
    public const PHONE_PATTERN = '/^\+?[0-9 ]{7,20}$/';

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
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:'.self::PHONE_PATTERN],
            'email' => ['nullable', 'email', 'max:150'],
            'relationship' => ['required', 'in:father,mother,guardian,other'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
