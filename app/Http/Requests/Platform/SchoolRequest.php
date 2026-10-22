<?php

namespace App\Http\Requests\Platform;

use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Creating a school (no route school) or changing one (route school `tenant`). Platform administrators only. */
class SchoolRequest extends FormRequest
{
    /** Short names that would collide with the platform's own addresses. */
    private const RESERVED = ['www', 'admin', 'api', 'app', 'mail', 'static', 'assets', 't', 'webhooks', 'platform', 'login', 'up'];

    public function authorize(): bool
    {
        return (bool) $this->user()?->isSuperAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => is_string($this->input('slug')) ? strtolower(trim($this->input('slug'))) : $this->input('slug'),
            'domain' => is_string($this->input('domain')) ? (strtolower(trim($this->input('domain'))) ?: null) : null,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $school = $this->route('tenant');
        $ignore = $school instanceof Tenant ? $school->id : null;

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'domain' => ['nullable', 'string', 'max:190', 'regex:/^([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,}$/', Rule::unique('tenants', 'domain')->ignore($ignore), Rule::notIn(config('tenancy.central_domains', []))],
            'subscription_plan_id' => ['required', 'integer', Rule::exists(SubscriptionPlan::class, 'id')],
        ];

        if ($school instanceof Tenant) {
            return $rules + [
                'subscription_status' => ['required', Rule::enum(SubscriptionStatus::class)],
                'grace_ends_at' => ['nullable', 'date', 'required_if:subscription_status,'.SubscriptionStatus::Grace->value],
            ];
        }

        return $rules + [
            'slug' => ['required', 'string', 'min:3', 'max:40', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::unique('tenants', 'slug'), Rule::notIn(self::RESERVED)],
            'currency' => ['required', 'string', Rule::in(array_keys(Money::CURRENCIES))],
            'owner_name' => ['required', 'string', 'max:150'],
            'owner_email' => ['required', 'email:rfc', 'max:190'],
        ];
    }
}
