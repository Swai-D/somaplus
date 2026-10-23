<?php

namespace App\Http\Controllers\Timetable;

use App\Domain\Timetable\CopyTimetable;
use App\Domain\Timetable\DeleteTimetableEntry;
use App\Domain\Timetable\SaveTimetableEntry;
use App\Domain\Timetable\TimetableGrid;
use App\Domain\Timetable\TimetableTerm;
use App\Http\Controllers\Controller;
use App\Http\Requests\Timetable\CopyRequest;
use App\Http\Requests\Timetable\SaveEntryRequest;
use App\Http\Requests\Timetable\ViewTimetableRequest;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function index(ViewTimetableRequest $request, TimetableGrid $grid, TimetableTerm $terms): View
    {
        $term = $this->termFrom($request) ?? $terms->current();
        $class = $request->filled('school_class_id') ? SchoolClass::query()->find($request->integer('school_class_id')) : null;
        $stream = $request->filled('stream_id') ? Stream::query()->find($request->integer('stream_id')) : null;

        return view('timetable.index', $this->choices() + [
            'term' => $term,
            'class' => $class,
            'stream' => $stream,
            'week' => $term !== null && $class !== null ? $grid->forClass($term, $class, $stream) : null,
            'canManage' => (bool) $request->user()?->can('create', TimetableEntry::class),
        ]);
    }

    public function teachers(ViewTimetableRequest $request, TimetableGrid $grid, TimetableTerm $terms): View
    {
        $term = $this->termFrom($request) ?? $terms->current();
        $teacher = $request->filled('teacher_id') ? Teacher::query()->with('user')->find($request->integer('teacher_id')) : null;

        return view('timetable.teachers', $this->choices() + [
            'term' => $term,
            'teacher' => $teacher,
            'week' => $term !== null && $teacher !== null ? $grid->forTeacher($term, $teacher) : null,
        ]);
    }

    public function mine(ViewTimetableRequest $request, TimetableGrid $grid, TimetableTerm $terms): View
    {
        $term = $this->termFrom($request) ?? $terms->current();
        $teacher = Teacher::query()->where('user_id', $request->user()?->id)->first();

        return view('timetable.mine', $this->choices() + [
            'term' => $term,
            'teacher' => $teacher,
            'week' => $term !== null && $teacher !== null ? $grid->forTeacher($term, $teacher) : null,
        ]);
    }

    public function edit(TimetableEntry $entry): View
    {
        Gate::authorize('update', $entry);

        return view('timetable.edit', $this->choices() + ['entry' => $entry]);
    }

    public function store(SaveEntryRequest $request, SaveTimetableEntry $save): RedirectResponse
    {
        /** @var array{term_id: int, school_class_id: int, stream_id?: int|null, subject_id: int, teacher_id?: int|null, timetable_period_id: int, weekday: int, room?: string|null} $data */
        $data = $request->validated();

        return $this->back($save->handle($data));
    }

    public function update(SaveEntryRequest $request, TimetableEntry $entry, SaveTimetableEntry $save): RedirectResponse
    {
        /** @var array{term_id: int, school_class_id: int, stream_id?: int|null, subject_id: int, teacher_id?: int|null, timetable_period_id: int, weekday: int, room?: string|null} $data */
        $data = $request->validated();

        return $this->back($save->handle($data, $entry));
    }

    public function destroy(TimetableEntry $entry, DeleteTimetableEntry $delete): RedirectResponse
    {
        Gate::authorize('delete', $entry);

        $delete->handle($entry);

        return $this->back($entry, 'timetable.deleted');
    }

    public function copy(CopyRequest $request, CopyTimetable $copy): RedirectResponse
    {
        $from = Term::query()->findOrFail($request->integer('from_term_id'));
        $to = Term::query()->findOrFail($request->integer('to_term_id'));

        $result = $copy->handle($from, $to);

        return redirect()->route('timetable.index', ['term_id' => $to->id])
            ->with('status', __('timetable.copied', $result));
    }

    private function termFrom(ViewTimetableRequest $request): ?Term
    {
        return $request->filled('term_id') ? Term::query()->find($request->integer('term_id')) : null;
    }

    /** Back to the class timetable the lesson belongs to. */
    private function back(TimetableEntry $entry, string $status = 'timetable.saved'): RedirectResponse
    {
        return redirect()->route('timetable.index', array_filter([
            'term_id' => $entry->term_id,
            'school_class_id' => $entry->school_class_id,
            'stream_id' => $entry->stream_id,
        ]))->with('status', __($status));
    }

    /**
     * @return array<string, mixed>
     */
    private function choices(): array
    {
        return [
            'terms' => Term::query()->with('academicYear')->orderByDesc('starts_on')->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
            'streams' => Stream::query()->with('schoolClass')->orderBy('school_class_id')->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->with('user')->where('status', Teacher::ACTIVE)->get()->sortBy(fn (Teacher $t): string => mb_strtolower($t->fullName()))->values(),
            'periods' => TimetablePeriod::query()->where('is_break', false)->orderBy('starts_at')->get(),
        ];
    }
}
