<?php

namespace App\Domain\Exams;

use App\Domain\Settings\RecordAudit;
use App\Models\Exam;
use App\Models\GradingScale;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates or changes an exam. Once marks exist, the term, grading scale and maximum mark cannot change,
 * and a locked exam cannot be changed at all, so published results stay reproducible.
 */
final class SaveExam
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, term_id: int|string, grading_scale_id: int|string, max_mark: int|string}  $data
     */
    public function handle(array $data, ?Exam $exam = null): Exam
    {
        return DB::transaction(function () use ($data, $exam): Exam {
            $term = Term::query()->findOrFail((int) $data['term_id']);
            // Shared lock: the scale cannot be edited while an exam is being created from it.
            $scale = GradingScale::query()->whereKey((int) $data['grading_scale_id'])->sharedLock()->firstOrFail();
            $max = (int) $data['max_mark'];

            if ($exam !== null) {
                $exam = Exam::query()->whereKey($exam->id)->lockForUpdate()->firstOrFail();

                if ($exam->isLocked()) {
                    throw ValidationException::withMessages(['exam' => __('exams.errors.locked')]);
                }

                $structural = $exam->term_id !== $term->id || $exam->grading_scale_id !== $scale->id || $exam->max_mark !== $max;
                if ($structural && $exam->marks()->exists()) {
                    throw ValidationException::withMessages(['max_mark' => __('exams.exams.has_marks')]);
                }
            }

            $creating = $exam === null;
            $exam ??= new Exam;
            $exam->fill(['name' => trim($data['name']), 'max_mark' => $max]);
            $exam->term_id = $term->id;
            $exam->academic_year_id = $term->academic_year_id;
            $exam->grading_scale_id = $scale->id;
            $exam->save();

            $this->audit->handle($creating ? 'exam.created' : 'exam.updated', $exam);

            return $exam;
        });
    }
}
