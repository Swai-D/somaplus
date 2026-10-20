<?php

namespace App\Http\Controllers\Exams;

use App\Domain\Exams\ReportCard;
use App\Domain\Exams\ResultSheet;
use App\Domain\Settings\RecordAudit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exams\ResultsQueryRequest;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(ResultsQueryRequest $request, Exam $exam, ResultSheet $sheet, RecordAudit $audit): View
    {
        $class = $request->filled('school_class_id') ? SchoolClass::query()->find($request->integer('school_class_id')) : null;
        $stream = $request->filled('stream_id') ? Stream::query()->find($request->integer('stream_id')) : null;

        $results = [];
        if ($class !== null && $exam->isLocked()) {
            $results = $sheet->rows($exam, $class, $stream);
            $audit->handle('results.viewed', $exam);
        }

        return view('exams.results.index', [
            'exam' => $exam->load(['term', 'academicYear', 'gradingScale.bands']),
            'class' => $class,
            'stream' => $stream,
            'results' => $results,
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
            'streams' => Stream::query()->with('schoolClass')->orderBy('school_class_id')->orderBy('name')->get(),
        ]);
    }

    public function report(Student $student, Term $term, ReportCard $card, RecordAudit $audit): View
    {
        Gate::authorize('viewReport', $student);

        $exams = $card->handle($student, $term);
        $audit->handle('report.viewed', $student);

        return view('exams.results.report', [
            'student' => $student,
            'term' => $term->load('academicYear'),
            'exams' => $exams,
        ]);
    }
}
