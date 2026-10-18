<?php

namespace App\Http\Controllers\Academics;

use App\Domain\Academics\DeleteAcademicRecord;
use App\Domain\Academics\SaveAcademicYear;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\AcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', AcademicYear::class);

        return view('academics.years.index', [
            'years' => AcademicYear::query()->withCount('terms')->orderByDesc('starts_on')->get(),
        ]);
    }

    public function show(AcademicYear $year): View
    {
        Gate::authorize('view', $year);

        return view('academics.years.show', ['year' => $year, 'terms' => $year->terms]);
    }

    public function edit(AcademicYear $year): View
    {
        Gate::authorize('update', $year);

        return view('academics.years.edit', ['year' => $year]);
    }

    public function store(AcademicYearRequest $request, SaveAcademicYear $save): RedirectResponse
    {
        /** @var array{name: string, starts_on: string, ends_on: string, is_current?: bool} $data */
        $data = $request->validated();
        $year = $save->handle($data);

        return redirect()->route('academics.years.show', $year)->with('status', __('academics.saved'));
    }

    public function update(AcademicYearRequest $request, AcademicYear $year, SaveAcademicYear $save): RedirectResponse
    {
        /** @var array{name: string, starts_on: string, ends_on: string, is_current?: bool} $data */
        $data = $request->validated();
        $save->handle($data, $year);

        return redirect()->route('academics.years.show', $year)->with('status', __('academics.saved'));
    }

    public function destroy(AcademicYear $year, DeleteAcademicRecord $delete): RedirectResponse
    {
        Gate::authorize('delete', $year);

        $delete->handle($year);

        return redirect()->route('academics.years.index')->with('status', __('academics.deleted'));
    }
}
