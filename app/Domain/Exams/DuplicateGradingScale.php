<?php

namespace App\Domain\Exams;

use App\Models\GradingBand;
use App\Models\GradingScale;

/**
 * Copies a scale (also a used one) under a new name, so it can be changed without touching past grades.
 */
final class DuplicateGradingScale
{
    public function __construct(private readonly SaveGradingScale $save) {}

    public function handle(GradingScale $scale): GradingScale
    {
        $base = mb_substr($scale->name, 0, 90).' ('.__('exams.scales.copy_suffix').')';
        $name = $base;
        for ($n = 2; GradingScale::query()->where('name', $name)->exists(); $n++) {
            $name = "{$base} {$n}";
        }

        return $this->save->handle([
            'name' => $name,
            'bands' => $scale->bands->map(fn (GradingBand $band): array => [
                'min_mark' => (string) $band->min_mark,
                'grade' => $band->grade,
                'points' => $band->points,
                'remark' => $band->remark,
            ])->all(),
        ]);
    }
}
