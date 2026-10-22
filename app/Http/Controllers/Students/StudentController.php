<?php

namespace App\Http\Controllers\Students;

use App\Domain\Settings\RecordAudit;
use App\Domain\Students\AdmissionNumbers;
use App\Domain\Students\ChangeStudentStatus;
use App\Domain\Students\CurrentPlacements;
use App\Domain\Students\SaveStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\LeaveRequest;
use App\Http\Requests\Students\StudentRequest;
use App\Models\AcademicYear;
use App\Models\AdmissionSetting;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request, CurrentPlacements $placements): View
    {
        Gate::authorize('viewAny', Student::class);

        $year = AcademicYear::query()->where('is_current', true)->first();
        $left = $request->query('status') === 'left';
        $classId = $request->integer('class') ?: null;

        $query = Student::query()
            ->where('status', $left ? Student::LEFT : Student::ACTIVE)
            ->orderBy('first_name')->orderBy('last_name')->orderBy('id');

        if ($classId !== null && $year !== null) {
            $latest = Enrollment::query()->selectRaw('max(id)')->where('academic_year_id', $year->id)->groupBy('student_id');
            $query->whereIn('id', Enrollment::query()
                ->select('student_id')
                ->where('academic_year_id', $year->id)
                ->where('school_class_id', $classId)
                ->whereIn('id', $latest));
        }

        $students = $query->paginate(25)->withQueryString();

        return $this->list($students->getCollection()->all(), $year, $placements, [
            'students' => $students, 'term' => null, 'left' => $left, 'classId' => $classId,
        ] + $this->choices() + $this->admission());
    }

    /**
     * Searching is a POST so the name or number typed never ends up in a URL, a log or the browser history.
     */
    public function search(Request $request, CurrentPlacements $placements, RecordAudit $audit): View
    {
        Gate::authorize('viewAny', Student::class);

        $term = trim((string) $request->validate(['term' => ['required', 'string', 'min:2', 'max:100']])['term']);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_strtolower($term)).'%';
        $year = AcademicYear::query()->where('is_current', true)->first();

        $students = Student::query()
            ->where(function ($q) use ($like): void {
                $q->whereRaw('lower(first_name) like ?', [$like])
                    ->orWhereRaw('lower(last_name) like ?', [$like])
                    ->orWhereRaw('lower(admission_number) like ?', [$like]);
            })
            ->orderBy('first_name')->orderBy('last_name')->limit(50)->get();

        $audit->handle('student.searched', new Student);

        return $this->list($students->all(), $year, $placements, [
            'students' => $students, 'term' => $term, 'left' => false, 'classId' => null,
        ] + $this->choices() + $this->admission());
    }

    /**
     * The form of a new student is a pop-up of the list, as in the template, so this address
     * only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', Student::class);

        return redirect()->route('students.index', ['add' => 'student-modal']);
    }

    public function store(StudentRequest $request, SaveStudent $save): RedirectResponse
    {
        $student = $save->handle($request->validated());

        return redirect()->route('students.show', $student)->with('status', __('students.saved'));
    }

    public function show(Student $student, RecordAudit $audit): View
    {
        Gate::authorize('view', $student);

        $audit->handle('student.viewed', $student);

        return view('students.show', [
            'student' => $student,
            'guardians' => $student->guardians()->get(),
            'history' => $student->enrollments()->with(['academicYear', 'schoolClass', 'stream'])->get(),
            // Terms with at least one locked exam in which this student has marks (report cards).
            'reportTerms' => Term::query()->with('academicYear')->orderByDesc('starts_on')
                ->whereIn('id', Exam::query()->select('term_id')->whereNotNull('locked_at')
                    ->whereIn('id', ExamMark::query()->select('exam_id')->where('student_id', $student->id)))
                ->get(),
        ] + $this->choices());
    }

    public function edit(Student $student): View
    {
        Gate::authorize('update', $student);

        return view('students.edit', ['student' => $student] + $this->admission());
    }

    public function update(StudentRequest $request, Student $student, SaveStudent $save): RedirectResponse
    {
        $save->handle($request->validated(), $student);

        return redirect()->route('students.show', $student)->with('status', __('students.saved'));
    }

    public function leave(LeaveRequest $request, Student $student, ChangeStudentStatus $status): RedirectResponse
    {
        /** @var array{left_on: string, left_reason?: string|null} $data */
        $data = $request->validated();
        $status->leave($student, $data['left_on'], $data['left_reason'] ?? null);

        return redirect()->route('students.show', $student)->with('status', __('students.saved'));
    }

    public function reinstate(Student $student, ChangeStudentStatus $status): RedirectResponse
    {
        Gate::authorize('update', $student);

        $status->reinstate($student);

        return redirect()->route('students.show', $student)->with('status', __('students.saved'));
    }

    /**
     * @param  list<Student>  $students
     * @param  array<string, mixed>  $data
     */
    private function list(array $students, ?AcademicYear $year, CurrentPlacements $placements, array $data): View
    {
        return view('students.index', $data + [
            'year' => $year,
            'placements' => $placements->for($students, $year),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    /**
     * @return array{admissionAuto: bool, admissionNext: string}
     */
    private function admission(): array
    {
        return ['admissionAuto' => AdmissionSetting::current()->isAuto(), 'admissionNext' => AdmissionNumbers::peek(now())];
    }

    /**
     * @return array{years: Collection<int, AcademicYear>, classes: Collection<int, SchoolClass>, streams: Collection<int, Stream>, currentYearId: int|null}
     */
    private function choices(): array
    {
        $years = AcademicYear::query()->orderByDesc('starts_on')->get();

        return [
            'years' => $years,
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
            'streams' => Stream::query()->with('schoolClass')->orderBy('school_class_id')->orderBy('name')->get(),
            'currentYearId' => $years->firstWhere('is_current', true)?->id,
        ];
    }
}
