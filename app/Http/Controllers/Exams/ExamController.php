<?php

namespace App\Http\Controllers\Exams;

use App\Domain\Exams\DeleteExam;
use App\Domain\Exams\LockExam;
use App\Domain\Exams\SaveExam;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exams\ExamRequest;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\GradingScale;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Exam::class);

        return view('exams.exams.index', [
            'exams' => Exam::query()->with(['term', 'academicYear', 'gradingScale'])->orderByDesc('id')->get(),
        ] + $this->choices());
    }

    /**
     * The form of a new exam is a pop-up of the list, as in the template, so this address
     * only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', Exam::class);

        return redirect()->route('exams.index', ['add' => 'exam-modal']);
    }

    public function store(ExamRequest $request, SaveExam $save): RedirectResponse
    {
        /** @var array{name: string, term_id: int|string, grading_scale_id: int|string, max_mark: int|string} $data */
        $data = $request->validated();
        $exam = $save->handle($data);

        return redirect()->route('exams.show', $exam)->with('status', __('exams.saved'));
    }

    public function show(Exam $exam): View
    {
        Gate::authorize('view', $exam);

        return view('exams.exams.show', [
            'exam' => $exam->load(['term', 'academicYear', 'gradingScale.bands']),
            'marksCount' => $exam->marks()->count(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
            'streams' => Stream::query()->with('schoolClass')->orderBy('school_class_id')->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(Exam $exam): View
    {
        Gate::authorize('update', $exam);

        return view('exams.exams.edit', ['exam' => $exam, 'hasMarks' => $exam->marks()->exists()] + $this->choices());
    }

    public function update(ExamRequest $request, Exam $exam, SaveExam $save): RedirectResponse
    {
        /** @var array{name: string, term_id: int|string, grading_scale_id: int|string, max_mark: int|string} $data */
        $data = $request->validated();
        $save->handle($data, $exam);

        return redirect()->route('exams.show', $exam)->with('status', __('exams.saved'));
    }

    public function destroy(Exam $exam, DeleteExam $delete): RedirectResponse
    {
        Gate::authorize('delete', $exam);

        $delete->handle($exam);

        return redirect()->route('exams.index')->with('status', __('exams.deleted'));
    }

    public function lock(Exam $exam, LockExam $lock): RedirectResponse
    {
        Gate::authorize('update', $exam);

        $lock->lock($exam);

        return redirect()->route('exams.show', $exam)->with('status', __('exams.exams.locked_now'));
    }

    public function unlock(Exam $exam, LockExam $lock): RedirectResponse
    {
        Gate::authorize('update', $exam);

        $lock->unlock($exam);

        return redirect()->route('exams.show', $exam)->with('status', __('exams.exams.unlocked_now'));
    }

    /**
     * @return array<string, mixed>
     */
    private function choices(): array
    {
        return [
            'years' => AcademicYear::query()->with('terms')->orderByDesc('starts_on')->get(),
            'scales' => GradingScale::query()->orderBy('name')->get(),
        ];
    }
}
