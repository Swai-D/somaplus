<?php

namespace App\Http\Requests\Settings;

use App\Domain\Settings\RoleCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        $id = $this->route('role');

        return [
            'name' => [
                'required', 'string', 'max:60', 'regex:/^[\pL\pN][\pL\pN .,\'&()\/-]*$/u',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (in_array(mb_strtolower(trim((string) $value)), RoleCatalog::reservedNames(), true)) {
                        $fail(__('custom_roles.name_reserved'));
                    }
                },
                // Two roles of a school may not differ only by capital letters.
                function (string $attribute, mixed $value, \Closure $fail) use ($id): void {
                    $taken = RoleCatalog::query()->whereRaw('lower(name) = ?', [mb_strtolower(trim((string) $value))])
                        ->when(is_scalar($id), fn ($query) => $query->whereKeyNot($id))->exists();

                    if ($taken) {
                        $fail(__('custom_roles.name_taken'));
                    }
                },
            ],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', Rule::in(RoleCatalog::assignable())],
        ];
    }
}
