<?php

namespace App\Http\Requests\Attendance;

use App\Models\AttendanceRecord;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * Base for the attendance forms: a class, an optional stream of that class and a date.
 * exists() queries the tables directly, outside the tenant scope, so it is limited to the school here.
 */
abstract class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', AttendanceRecord::class);
    }

    protected function inSchool(string $table): Exists
    {
        return Rule::exists($table, 'id')->where('tenant_id', app(TenantContext::class)->currentId());
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function classRules(): array
    {
        return [
            'school_class_id' => ['required', 'integer', $this->inSchool('school_classes')],
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id'))],
        ];
    }
}
