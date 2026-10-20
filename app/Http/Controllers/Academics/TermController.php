<?php

namespace App\Http\Controllers\Academics;

use App\Domain\Academics\DeleteAcademicRecord;
use App\Domain\Academics\SaveTerm;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\TermRequest;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TermController extends Controller
{
    public function edit(Term $term): View
    {
        Gate::authorize('update', $term);

        return view('academics.terms.edit', ['term' => $term, 'year' => $term->academicYear]);
    }

    public function store(TermRequest $request, AcademicYear $year, SaveTerm $save): RedirectResponse
    {
        /** @var array{name: string, starts_on: string, ends_on: string} $data */
        $data = $request->validated();
        $save->handle($year, $data);

        return redirect()->route('academics.years.show', $year)->with('status', __('academics.saved'));
    }

    public function update(TermRequest $request, Term $term, SaveTerm $save): RedirectResponse
    {
        /** @var array{name: string, starts_on: string, ends_on: string} $data */
        $data = $request->validated();
        $year = $term->academicYear;
        $save->handle($year, $data, $term);

        return redirect()->route('academics.years.show', $year)->with('status', __('academics.saved'));
    }

    public function destroy(Term $term, DeleteAcademicRecord $delete): RedirectResponse
    {
        Gate::authorize('delete', $term);

        $year = $term->academicYear;
        $delete->handle($term);

        return redirect()->route('academics.years.show', $year)->with('status', __('academics.deleted'));
    }
}
