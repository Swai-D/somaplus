<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One student's score in one subject of one exam. The grade is never stored.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $exam_id
 * @property int $subject_id
 * @property int $student_id
 * @property int $school_class_id
 * @property int|null $stream_id
 * @property string|null $score
 * @property bool $is_absent
 * @property int|null $entered_by
 */
class ExamMark extends Model
{
    use BelongsToTenant;

    protected $fillable = ['subject_id', 'student_id', 'school_class_id', 'stream_id', 'score', 'is_absent', 'entered_by'];

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'is_absent' => 'boolean'];
    }

    /**
     * @return BelongsTo<Exam, $this>
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
