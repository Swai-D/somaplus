<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\TimetableEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One lesson of a term's timetable.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $term_id
 * @property int $school_class_id
 * @property int|null $stream_id
 * @property int $subject_id
 * @property int|null $teacher_id
 * @property int $timetable_period_id
 * @property int $weekday ISO: 1 = Monday ... 7 = Sunday
 * @property string|null $room
 * @property int|null $created_by
 */
class TimetableEntry extends Model
{
    /** @use HasFactory<TimetableEntryFactory> */
    use BelongsToTenant, HasFactory;

    /** @var list<int> */
    public const WEEKDAYS = [1, 2, 3, 4, 5, 6, 7];

    /** The days always shown; Saturday and Sunday appear only when they hold a lesson. */
    public const SCHOOL_DAYS = [1, 2, 3, 4, 5];

    protected $fillable = [
        'term_id', 'school_class_id', 'stream_id', 'subject_id', 'teacher_id',
        'timetable_period_id', 'weekday', 'room', 'created_by',
    ];

    protected function casts(): array
    {
        return ['weekday' => 'integer'];
    }

    /**
     * @return BelongsTo<Term, $this>
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * @return BelongsTo<Stream, $this>
     */
    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * @return BelongsTo<TimetablePeriod, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(TimetablePeriod::class, 'timetable_period_id');
    }

    /** "Form 1" or "Form 1 – A" */
    public function groupLabel(): string
    {
        $class = $this->schoolClass->name ?? '';

        return $this->stream === null ? $class : $class.' – '.$this->stream->name;
    }
}
