<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Settings\SaveSchoolProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\SchoolProfileRequest;
use App\Models\SchoolProfile;
use App\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SchoolProfileController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage', SchoolProfile::class);

        return view('settings.school', ['profile' => SchoolProfile::current(), 'school' => app(TenantContext::class)->current()]);
    }

    public function update(SchoolProfileRequest $request, SaveSchoolProfile $save): RedirectResponse
    {
        /** @var array{phone?: string|null, email?: string|null, address?: string|null} $data */
        $data = $request->validated();
        $save->handle($data);

        return redirect()->route('settings.school.index')->with('status', __('school.saved'));
    }
}
