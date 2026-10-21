<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Students\AdmissionNumbers;
use App\Domain\Students\SaveAdmissionSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\AdmissionSettingRequest;
use App\Models\AdmissionSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdmissionSettingController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage', AdmissionSetting::class);

        return view('settings.admission', [
            'setting' => AdmissionSetting::current(),
            'next' => AdmissionNumbers::peek(now()),
            'year' => now()->year,
        ]);
    }

    public function update(AdmissionSettingRequest $request, SaveAdmissionSettings $save): RedirectResponse
    {
        /** @var array{mode: string, prefix?: string|null, separator?: string|null, year_format?: string|null, digits?: int|string|null, start_at?: int|string|null} $data */
        $data = $request->validated();
        $save->handle($data);

        return redirect()->route('settings.admission.index')->with('status', __('admission.saved'));
    }
}
