<?php

namespace App\Http\Controllers\Students;

use App\Domain\Students\BuildIdCards;
use App\Domain\Students\CurrentPlacements;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StudentIdCardRequest;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Print ID cards: one student from their page, or a class (or a chosen few) from the print page. */
class StudentIdCardController extends Controller
{
    public function index(Request $request, CurrentPlacements $placements): View
    {
        Gate::authorize('printIdCard', Student::class);

        $max = (int) config('students.id_card_max');
        $year = AcademicYear::query()->where('is_current', true)->first();
        $classes = SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get();
        $classId = $request->integer('class');

        $students = collect();
        if ($year !== null && $classId > 0) {
            $ids = Enrollment::query()->where('academic_year_id', $year->id)->where('school_class_id', $classId)->pluck('student_id');
            $students = Student::query()->where('status', 'active')->whereIn('id', $ids)->orderBy('first_name')->orderBy('last_name')->limit($max)->get();
        } elseif ($year !== null && $request->query('class') === 'all') {
            $students = Student::query()->where('status', 'active')->orderBy('first_name')->orderBy('last_name')->limit($max)->get();
        }

        return view('students.id-cards', [
            'year' => $year,
            'classes' => $classes,
            'selected' => $request->query('class') === 'all' ? 'all' : ($classId > 0 ? (string) $classId : ''),
            'students' => $students,
            'placements' => $placements->for($students, $year),
            'max' => $max,
        ]);
    }

    public function print(StudentIdCardRequest $request, BuildIdCards $cards): View
    {
        $students = Student::query()->where('status', 'active')->whereIn('id', $request->studentIds())->orderBy('first_name')->orderBy('last_name')->get();
        abort_if($students->isEmpty(), 404);

        return $this->sheet($cards, $students);
    }

    public function show(Student $student, BuildIdCards $cards): View
    {
        Gate::authorize('printIdCard', Student::class);
        abort_unless($student->isActive(), 404);

        return $this->sheet($cards, collect([$student]));
    }

    /**
     * @param  Collection<int, Student>  $students
     */
    private function sheet(BuildIdCards $cards, Collection $students): View
    {
        return view('students.id-cards-print', [
            'cards' => $cards->handle($students),
            'school' => app(TenantContext::class)->current()?->name,
        ]);
    }
}
