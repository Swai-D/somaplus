<?php

namespace App\Http\Controllers\Academics;

use App\Domain\Academics\DeleteAcademicRecord;
use App\Domain\Academics\SaveSubject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\SubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Subject::class);

        return view('academics.subjects.index', ['subjects' => Subject::query()->orderBy('name')->get()]);
    }

    public function edit(Subject $subject): View
    {
        Gate::authorize('update', $subject);

        return view('academics.subjects.edit', ['subject' => $subject]);
    }

    public function store(SubjectRequest $request, SaveSubject $save): RedirectResponse
    {
        /** @var array{name: string, code?: string|null} $data */
        $data = $request->validated();
        $save->handle($data);

        return redirect()->route('academics.subjects.index')->with('status', __('academics.saved'));
    }

    public function update(SubjectRequest $request, Subject $subject, SaveSubject $save): RedirectResponse
    {
        /** @var array{name: string, code?: string|null} $data */
        $data = $request->validated();
        $save->handle($data, $subject);

        return redirect()->route('academics.subjects.index')->with('status', __('academics.saved'));
    }

    public function destroy(Subject $subject, DeleteAcademicRecord $delete): RedirectResponse
    {
        Gate::authorize('delete', $subject);

        $delete->handle($subject);

        return redirect()->route('academics.subjects.index')->with('status', __('academics.deleted'));
    }
}
