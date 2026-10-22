<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A pupil of the school. Personal data of a minor: collect only what is needed
 * and never put it in logs, error tracking or URLs.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $admission_number
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string $gender
 * @property CarbonImmutable $date_of_birth
 * @property CarbonImmutable $admitted_on
 * @property string $status
 * @property CarbonImmutable|null $left_on
 * @property string|null $left_reason
 */
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use BelongsToTenant, HasFactory;

    public const ACTIVE = 'active';

    public const LEFT = 'left';

    protected $attributes = ['status' => self::ACTIVE];

    protected $fillable = [
        'admission_number', 'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth', 'admitted_on',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'immutable_date',
            'admitted_on' => 'immutable_date',
            'left_on' => 'immutable_date',
        ];
    }

    public function fullName(): string
    {
        return implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name]));
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    /**
     * Newest placement first.
     *
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->orderByDesc('id');
    }

    /**
     * @return BelongsToMany<Guardian, $this>
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class)
            ->withPivot(['relationship', 'is_primary'])
            ->withTimestamps()
            ->orderByDesc('guardian_student.is_primary')
            ->orderBy('guardians.name');
    }

    /**
     * The card of the library the pupil holds, when they are a member of it.
     *
     * @return HasOne<LibraryMember, $this>
     */
    public function libraryMember(): HasOne
    {
        return $this->hasOne(LibraryMember::class);
    }
}
