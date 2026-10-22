<?php

namespace App\Http\Requests\Teachers;

use App\Models\Teacher;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->route('teacher');

        return $teacher instanceof Teacher
            ? (bool) $this->user()?->can('update', $teacher)
            : (bool) $this->user()?->can('create', Teacher::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $teacher = $this->route('teacher');
        $tenantId = TenantContext::instance()->currentId();

        // The account of the teacher is the one whose address has to stay free.
        $userId = $teacher instanceof Teacher ? $teacher->user_id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->where('tenant_id', $tenantId)->ignore($userId),
            ],
            'password' => [$teacher instanceof Teacher ? 'nullable' : 'required', 'string', 'min:8', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'staff_number' => [
                'nullable', 'string', 'max:30',
                Rule::unique('teachers', 'staff_number')->where('tenant_id', $tenantId)->ignore($teacher instanceof Teacher ? $teacher->id : null),
            ],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:150'],
            'contract_type' => ['nullable', Rule::in(['permanent', 'contractual', 'part_time', 'hourly'])],
            'shift' => ['nullable', Rule::in(['day', 'evening', 'night'])],
            'joined_on' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => [Rule::exists('subjects', 'id')->where('tenant_id', $tenantId)],
            'classes' => ['nullable', 'array'],
            'classes.*' => [Rule::exists('school_classes', 'id')->where('tenant_id', $tenantId)],
        ];
    }
}
