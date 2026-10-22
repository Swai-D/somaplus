<?php

namespace App\Http\Controllers\Exams;

use App\Domain\Exams\DeleteGradingScale;
use App\Domain\Exams\DuplicateGradingScale;
use App\Domain\Exams\SaveGradingScale;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exams\GradingScaleRequest;
use App\Models\GradingScale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GradingScaleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', GradingScale::class);

        return view('exams.scales.index', [
            'scales' => GradingScale::query()->with('bands')->withCount('exams')->orderBy('name')->get(),
        ]);
    }

    /**
     * The form of a new grading scale is a pop-up of the list, as in the template, so this
     * address only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', GradingScale::class);

        return redirect()->route('grading-scales.index', ['add' => 'scale-modal']);
    }

    public function store(GradingScaleRequest $request, SaveGradingScale $save): RedirectResponse
    {
        /** @var array{name: string, bands: array<int, array<string, mixed>>} $data */
        $data = $request->validated();
        $save->handle($data);

        return redirect()->route('grading-scales.index')->with('status', __('exams.saved'));
    }

    public function edit(GradingScale $scale): View
    {
        Gate::authorize('update', $scale);

        return view('exams.scales.edit', ['scale' => $scale->load('bands'), 'used' => $scale->isUsed()]);
    }

    public function update(GradingScaleRequest $request, GradingScale $scale, SaveGradingScale $save): RedirectResponse
    {
        /** @var array{name: string, bands: array<int, array<string, mixed>>} $data */
        $data = $request->validated();
        $save->handle($data, $scale);

        return redirect()->route('grading-scales.index')->with('status', __('exams.saved'));
    }

    public function copy(GradingScale $scale, DuplicateGradingScale $duplicate): RedirectResponse
    {
        Gate::authorize('create', GradingScale::class);

        $copy = $duplicate->handle($scale->load('bands'));

        return redirect()->route('grading-scales.edit', $copy)->with('status', __('exams.saved'));
    }

    public function destroy(GradingScale $scale, DeleteGradingScale $delete): RedirectResponse
    {
        Gate::authorize('delete', $scale);

        $delete->handle($scale);

        return redirect()->route('grading-scales.index')->with('status', __('exams.deleted'));
    }
}
