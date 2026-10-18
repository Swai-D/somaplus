<?php

namespace App\Http\Controllers\Academics;

use App\Domain\Academics\DeleteAcademicRecord;
use App\Domain\Academics\SaveSchoolClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\SchoolClassRequest;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', SchoolClass::class);

        return view('academics.classes.index', [
            'classes' => SchoolClass::query()->withCount('streams')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function show(SchoolClass $schoolClass): View
    {
        Gate::authorize('view', $schoolClass);

        return view('academics.classes.show', ['class' => $schoolClass, 'streams' => $schoolClass->streams]);
    }

    public function edit(SchoolClass $schoolClass): View
    {
        Gate::authorize('update', $schoolClass);

        return view('academics.classes.edit', ['class' => $schoolClass]);
    }

    public function store(SchoolClassRequest $request, SaveSchoolClass $save): RedirectResponse
    {
        /** @var array{name: string, sort_order?: int|null} $data */
        $data = $request->validated();
        $class = $save->handle($data);

        return redirect()->route('academics.classes.show', $class)->with('status', __('academics.saved'));
    }

    public function update(SchoolClassRequest $request, SchoolClass $schoolClass, SaveSchoolClass $save): RedirectResponse
    {
        /** @var array{name: string, sort_order?: int|null} $data */
        $data = $request->validated();
        $save->handle($data, $schoolClass);

        return redirect()->route('academics.classes.show', $schoolClass)->with('status', __('academics.saved'));
    }

    public function destroy(SchoolClass $schoolClass, DeleteAcademicRecord $delete): RedirectResponse
    {
        Gate::authorize('delete', $schoolClass);

        $delete->handle($schoolClass);

        return redirect()->route('academics.classes.index')->with('status', __('academics.deleted'));
    }
}
