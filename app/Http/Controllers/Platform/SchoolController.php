<?php

namespace App\Http\Controllers\Platform;

use App\Domain\Platform\CreateSchool;
use App\Domain\Platform\UpdateSchool;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\SchoolRequest;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** The platform administrator's list of schools (central address only, see `platform.admin`). */
class SchoolController extends Controller
{
    public function index(): View
    {
        return view('platform.schools.index', ['schools' => Tenant::query()->with('plan')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('platform.schools.create', $this->options());
    }

    public function store(SchoolRequest $request, CreateSchool $create): RedirectResponse
    {
        /** @var array{name: string, slug: string, domain?: string|null, subscription_plan_id: int, currency: string, owner_name: string, owner_email: string} $data */
        $data = $request->validated();
        $result = $create->handle($data);

        return redirect()->route('platform.schools.edit', $result['school'])
            ->with('status', __($result['link_sent'] ? 'platform.created_sent' : 'platform.created_not_sent'));
    }

    public function edit(Tenant $tenant): View
    {
        return view('platform.schools.edit', $this->options() + ['school' => $tenant]);
    }

    public function update(SchoolRequest $request, Tenant $tenant, UpdateSchool $update): RedirectResponse
    {
        /** @var array{name: string, domain?: string|null, subscription_plan_id: int, subscription_status: string, grace_ends_at?: string|null} $data */
        $data = $request->validated();
        $update->handle($tenant, $data);

        return redirect()->route('platform.schools.edit', $tenant)->with('status', __('platform.saved'));
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'plans' => SubscriptionPlan::query()->orderBy('sort_order')->get(),
            'currencies' => array_keys(Money::CURRENCIES),
            'statuses' => SubscriptionStatus::cases(),
            'baseDomain' => config('tenancy.base_domain'),
        ];
    }
}
