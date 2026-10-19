<?php

namespace App\Http\Requests\Students;

use App\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

/**
 * Base for the forms of students. unique() and exists() query the tables
 * directly, outside the tenant scope, so they are limited to the current school here.
 */
abstract class StudentsRequest extends FormRequest
{
    protected function unique(string $table, string $column, ?int $ignoreId = null): Unique
    {
        $rule = Rule::unique($table, $column)->where('tenant_id', app(TenantContext::class)->currentId());

        return $ignoreId === null ? $rule : $rule->ignore($ignoreId);
    }

    protected function inSchool(string $table): Exists
    {
        return Rule::exists($table, 'id')->where('tenant_id', app(TenantContext::class)->currentId());
    }

    /**
     * Rules for a placement: year, class and an optional stream that belongs to that class.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function placementRules(bool $required): array
    {
        $presence = $required ? 'required' : 'nullable';

        return [
            'academic_year_id' => [$required ? 'required' : 'required_with:school_class_id', 'integer', $this->inSchool('academic_years')],
            'school_class_id' => [$presence, 'integer', $this->inSchool('school_classes')],
            'stream_id' => [
                'nullable', 'integer',
                $this->inSchool('streams')->where('school_class_id', $this->input('school_class_id')),
            ],
        ];
    }
}
