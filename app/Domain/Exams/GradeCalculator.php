<?php

namespace App\Domain\Exams;

use App\Models\GradingBand;
use App\Models\GradingScale;

/**
 * Works out the grade of a score from a grading scale, on the server and in whole hundredths.
 * The percentage of a score is score / max * 100, and the band that applies is the one with the
 * highest lower bound that the percentage reaches. A scale always has a band that starts at 0,
 * so every valid score gets a grade.
 */
final class GradeCalculator
{
    /**
     * Index of the band that applies, from lower bounds given in ascending order (in hundredths of a percent).
     *
     * @param  list<int>  $ascendingMinimums
     */
    public static function bandIndex(array $ascendingMinimums, int $scoreHundredths, int $maxMark): ?int
    {
        return self::averageBandIndex($ascendingMinimums, $scoreHundredths, 1, $maxMark);
    }

    /**
     * Index of the band for an average: `totalHundredths` is the sum of `count` scores, each out of `maxMark`.
     * The average is never rounded or divided: total / count / max * 100 >= minimum / 100 is multiplied out,
     * so 79.995 is below 80 and the grade is exact.
     *
     * @param  list<int>  $ascendingMinimums
     */
    public static function averageBandIndex(array $ascendingMinimums, int $totalHundredths, int $count, int $maxMark): ?int
    {
        if ($count <= 0 || $maxMark <= 0 || $totalHundredths < 0 || $totalHundredths > $maxMark * 100 * $count) {
            return null;
        }

        $found = null;
        foreach ($ascendingMinimums as $index => $minimum) {
            if ($totalHundredths * 100 >= $minimum * $maxMark * $count) {
                $found = $index;
            }
        }

        return $found;
    }

    public function grade(GradingScale $scale, int $scoreHundredths, int $maxMark): ?GradingBand
    {
        $bands = $scale->bands->values();
        $index = self::bandIndex(
            $bands->map(fn (GradingBand $band): int => Score::fromDecimal($band->min_mark))->all(),
            $scoreHundredths,
            $maxMark,
        );

        return $index === null ? null : $bands->get($index);
    }

    public function gradeAverage(GradingScale $scale, int $totalHundredths, int $count, int $maxMark): ?GradingBand
    {
        $bands = $scale->bands->values();
        $index = self::averageBandIndex(
            $bands->map(fn (GradingBand $band): int => Score::fromDecimal($band->min_mark))->all(),
            $totalHundredths,
            $count,
            $maxMark,
        );

        return $index === null ? null : $bands->get($index);
    }
}
