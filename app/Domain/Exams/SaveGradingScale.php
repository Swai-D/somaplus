<?php

namespace App\Domain\Exams;

use App\Domain\Settings\RecordAudit;
use App\Models\GradingBand;
use App\Models\GradingScale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates or replaces a grading scale and its bands.
 *
 * Each band is the lowest percentage that earns its grade, so bands can never leave a gap or overlap:
 * the grade of a percentage is the band with the highest lower bound that it reaches. The lowest band
 * must start at 0 so every score has a grade. A scale that an exam uses is never changed (its grades
 * must stay reproducible); copy it and change the copy.
 */
final class SaveGradingScale
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, bands: array<int|string, array{min_mark?: mixed, grade?: mixed, points?: mixed, remark?: mixed}>}  $data
     */
    public function handle(array $data, ?GradingScale $scale = null): GradingScale
    {
        $bands = $this->normalise($data['bands']);

        return DB::transaction(function () use ($data, $scale, $bands): GradingScale {
            if ($scale !== null) {
                // Locks the scale while an exam might be created from it at the same moment.
                GradingScale::query()->whereKey($scale->id)->lockForUpdate()->firstOrFail();

                if ($scale->isUsed()) {
                    throw ValidationException::withMessages(['name' => __('exams.scales.used')]);
                }

                $scale->bands()->delete();
            }

            $creating = $scale === null;
            $scale ??= new GradingScale;
            $scale->fill(['name' => trim($data['name'])])->save();

            foreach ($bands as $row) {
                $band = new GradingBand([
                    'min_mark' => Score::toDecimal($row['min']),
                    'grade' => $row['grade'],
                    'points' => $row['points'],
                    'remark' => $row['remark'],
                ]);
                $band->grading_scale_id = $scale->id;
                $band->save();
            }

            $this->audit->handle($creating ? 'grading_scale.created' : 'grading_scale.updated', $scale);

            return $scale;
        });
    }

    /**
     * @param  array<int|string, array{min_mark?: mixed, grade?: mixed, points?: mixed, remark?: mixed}>  $bands
     * @return list<array{min: int, grade: string, points: int|null, remark: string|null}>
     */
    private function normalise(array $bands): array
    {
        $rows = [];
        $minimums = [];
        $grades = [];

        foreach ($bands as $band) {
            $min = Score::parse((string) ($band['min_mark'] ?? ''));
            $grade = trim((string) ($band['grade'] ?? ''));
            $points = $band['points'] ?? null;
            $remark = trim((string) ($band['remark'] ?? ''));

            if ($min === null || $min > 10000) {
                $this->fail('exams.scales.errors.min');
            }
            if ($grade === '' || mb_strlen($grade) > 5) {
                $this->fail('exams.scales.errors.grade');
            }
            if ($points !== null && $points !== '' && (filter_var($points, FILTER_VALIDATE_INT) === false || (int) $points < 0 || (int) $points > 20)) {
                $this->fail('exams.scales.errors.points');
            }
            if (mb_strlen($remark) > 100) {
                $this->fail('exams.scales.errors.remark');
            }
            if (in_array($min, $minimums, true)) {
                $this->fail('exams.scales.errors.duplicate_min');
            }
            if (in_array(mb_strtolower($grade), $grades, true)) {
                $this->fail('exams.scales.errors.duplicate_grade');
            }

            $minimums[] = $min;
            $grades[] = mb_strtolower($grade);
            $rows[] = [
                'min' => $min,
                'grade' => $grade,
                'points' => ($points === null || $points === '') ? null : (int) $points,
                'remark' => $remark === '' ? null : $remark,
            ];
        }

        if ($rows === []) {
            $this->fail('exams.scales.errors.empty');
        }
        if (min($minimums) !== 0) {
            $this->fail('exams.scales.errors.zero');
        }

        usort($rows, fn (array $a, array $b): int => $a['min'] <=> $b['min']);

        return $rows;
    }

    private function fail(string $key): never
    {
        throw ValidationException::withMessages(['bands' => __($key)]);
    }
}
