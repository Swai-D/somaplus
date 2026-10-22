<?php

namespace App\Http\Requests\Settings;

use App\Domain\Settings\RoleCatalog;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('settings.manage');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        // The route gives the person as a model, so the id is taken from the model.
        $person = $this->route('user');
        $id = $person instanceof User ? $person->getKey() : $person;
        $creating = ! is_scalar($id);

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->where('tenant_id', TenantContext::instance()->currentId())->ignore(is_scalar($id) ? $id : null),
            ],
            'password' => [$creating ? 'required' : 'nullable', 'string', 'min:8', 'max:100'],
            'role' => ['required', 'string', Rule::in(RoleCatalog::query()->pluck('name')->all())],
        ];
    }
}
