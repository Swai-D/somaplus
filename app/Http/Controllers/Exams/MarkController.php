<?php

namespace App\Http\Controllers\Exams;

use App\Domain\Exams\MarkSheet;
use App\Domain\Exams\RecordMarks;
use App\Domain\Exams\TeachingAssignments;
use App\Domain\Settings\RecordAudit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exams\MarkQueryRequest;
use App\Http\Requests\Exams\MarksRequest;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MarkController extends Controller
{
    public function index(MarkQueryRequest $request, Exam $exam, MarkSheet $sheet, RecordAudit $audit, TenantContext $tenant, TeachingAssignments $assignments): View
    {
        $class = $request->filled('school_class_id') ? SchoolClass::query()->find($request->integer('school_class_id')) : null;
        $stream = $request->filled('stream_id') ? Stream::query()->find($request->integer('stream_id')) : null;
        $subject = $request->filled('subject_id') ? Subject::query()->find($request->integer('subject_id')) : null;

        // A teacher enters marks only for the subjects and the classes the school assigned
        // them; the owner and the head teacher keep everything.
        $restrictions = $assignments->restrictions($request->user());

        $rows = [];
        if ($class !== null && $subject !== null) {
            $rows = $sheet->rows($exam, $class, $stream, $subject);
            $audit->handle('marks.viewed', $exam);
        }

        return view('exams.marks.index', [
            'exam' => $exam->load(['term', 'academicYear']),
            'class' => $class,
            'stream' => $stream,
            'subject' => $subject,
            'rows' => $rows,
            'editable' => ! $exam->isLocked()
                && (bool) $request->user()?->can('enterMarks', $exam)
                && ($class === null && $subject === null
                    || $class !== null && $subject !== null && $assignments->teachesClass($restrictions, $class->id) && $assignments->teachesSubject($restrictions, $subject->id))
                && ! ($tenant->current()?->isReadOnly() ?? false),
            'classes' => $restrictions === null
                ? SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get()
                : $restrictions->classes()->orderBy('sort_order')->orderBy('name')->get(),
            'streams' => $restrictions === null
                ? Stream::query()->with('schoolClass')->orderBy('school_class_id')->orderBy('name')->get()
                : Stream::query()->with('schoolClass')->whereIn('school_class_id', $restrictions->classes()->pluck('school_classes.id'))->orderBy('school_class_id')->orderBy('name')->get(),
            'subjects' => $restrictions === null
                ? Subject::query()->orderBy('name')->get()
                : $restrictions->subjects()->orderBy('name')->get(),
        ]);
    }

    public function store(MarksRequest $request, Exam $exam, RecordMarks $record): RedirectResponse
    {
        $class = SchoolClass::query()->findOrFail($request->integer('school_class_id'));
        $stream = $request->filled('stream_id') ? Stream::query()->findOrFail($request->integer('stream_id')) : null;
        $subject = Subject::query()->findOrFail($request->integer('subject_id'));

        /** @var array<int|string, array{score?: mixed, absent?: mixed}> $rows */
        $rows = $request->validated('rows');
        $record->handle($exam, $class, $stream, $subject, $rows);

        return redirect()->route('exams.marks.index', [$exam] + array_filter([
            'school_class_id' => $class->id, 'stream_id' => $stream?->id, 'subject_id' => $subject->id,
        ]))->with('status', __('exams.marks.saved'));
    }
}
