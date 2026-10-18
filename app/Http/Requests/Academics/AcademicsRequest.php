<?php

namespace App\Http\Requests\Academics;

use App\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

/**
 * Base for the forms of the academic structure. The unique rule queries the
 * table directly, outside the tenant scope, so it is limited to the current
 * school here.
 */
abstract class AcademicsRequest extends FormRequest
{
    protected function unique(string $table, string $column, ?int $ignoreId = null): Unique
    {
        $rule = Rule::unique($table, $column)
            ->where('tenant_id', app(TenantContext::class)->currentId());

        return $ignoreId === null ? $rule : $rule->ignore($ignoreId);
    }
}
