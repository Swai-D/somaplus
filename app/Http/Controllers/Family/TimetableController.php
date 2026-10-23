<?php

namespace App\Http\Controllers\Family;

use App\Domain\Family\FamilyChildren;
use App\Domain\Timetable\TimetableGrid;
use App\Domain\Timetable\TimetableTerm;
use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** A parent's read-only view of the weekly timetable of their own child. Another child answers 404. */
class TimetableController extends Controller
{
    public function __invoke(Request $request, int $student, FamilyChildren $children, TimetableGrid $grid, TimetableTerm $terms): View
    {
        $child = $children->find($request->user(), $student);
        abort_if($child === null, 404);

        $termId = $request->query('term_id');
        $term = is_numeric($termId) ? Term::query()->find((int) $termId) : null;
        $term ??= $terms->current();

        return view('family.timetable', [
            'student' => $child,
            'term' => $term,
            'terms' => Term::query()->with('academicYear')->orderByDesc('starts_on')->get(),
            'week' => $term === null ? null : $grid->forStudent($term, $child),
        ]);
    }
}
