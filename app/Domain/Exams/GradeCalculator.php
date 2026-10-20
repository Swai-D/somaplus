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
        if ($maxMark <= 0 || $scoreHundredths < 0 || $scoreHundredths > $maxMark * 100) {
            return null;
        }

        $found = null;
        foreach ($ascendingMinimums as $index => $minimum) {
            // score / max * 100 >= minimum / 100, multiplied out so that nothing is divided.
            if ($scoreHundredths * 10000 >= $minimum * $maxMark * 100) {
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
}
