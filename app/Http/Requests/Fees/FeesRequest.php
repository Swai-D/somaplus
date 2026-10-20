<?php

namespace App\Http\Requests\Fees;

use App\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

/**
 * Base for the fee forms. unique() and exists() query the tables directly, outside the tenant
 * scope, so they are limited to the current school here.
 */
abstract class FeesRequest extends FormRequest
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
}
