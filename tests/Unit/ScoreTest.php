<?php

use App\Domain\Exams\GradeCalculator;
use App\Domain\Exams\Score;

it('reads typed marks exactly', function (string $input, int $hundredths) {
    expect(Score::parse($input))->toBe($hundredths);
})->with([
    ['0', 0],
    ['45', 4500],
    ['45.5', 4550],
    ['45,5', 4550],
    ['45.05', 4505],
    ['45.50', 4550],
    ['100', 10000],
    ['  72  ', 7200],
    ['007', 700],
    ['1000', 100000],
    ['0.01', 1],
    ['99.99', 9999],
]);

it('refuses anything that is not a plain mark', function (string $input) {
    expect(Score::parse($input))->toBeNull();
})->with([
    '', ' ', 'abc', '-1', '+5', '45.555', '1e2', '4 5', '45.', '.5', '0x1F', 'NaN', 'INF', '45,5,5', '1,000.5',
    '10000', '45%', "45\n6", '٤٥', '１２', '1; drop table marks', '45 .5',
]);

it('formats marks for people and for the database', function () {
    expect(Score::format(4550))->toBe('45.5')
        ->and(Score::format(4500))->toBe('45')
        ->and(Score::format(4505))->toBe('45.05')
        ->and(Score::format(0))->toBe('0')
        ->and(Score::format(10000))->toBe('100')
        ->and(Score::toDecimal(4550))->toBe('45.50')
        ->and(Score::toDecimal(5))->toBe('0.05')
        ->and(Score::toDecimal(0))->toBe('0.00')
        ->and(Score::fromDecimal('45.50'))->toBe(4550)
        ->and(Score::fromDecimal(45.5))->toBe(4550)
        ->and(Score::fromDecimal('0.29'))->toBe(29)
        ->and(Score::fromDecimal('1.15'))->toBe(115);
});

it('round-trips every hundredth up to a thousand', function () {
    for ($h = 0; $h <= 100000; $h += 7) {
        expect(Score::parse(Score::toDecimal($h)))->toBe($h)
            ->and(Score::fromDecimal(Score::toDecimal($h)))->toBe($h);
    }
});

// Lower bounds of A, B, C, D, F in hundredths of a percent.
$standard = [0, 5000, 6000, 7000, 8000];

it('puts a score exactly on a boundary in the higher band', function () use ($standard) {
    expect(GradeCalculator::bandIndex($standard, 8000, 100))->toBe(4)
        ->and(GradeCalculator::bandIndex($standard, 7999, 100))->toBe(3)
        ->and(GradeCalculator::bandIndex($standard, 7000, 100))->toBe(3)
        ->and(GradeCalculator::bandIndex($standard, 6999, 100))->toBe(2)
        ->and(GradeCalculator::bandIndex($standard, 5000, 100))->toBe(1)
        ->and(GradeCalculator::bandIndex($standard, 4999, 100))->toBe(0)
        ->and(GradeCalculator::bandIndex($standard, 0, 100))->toBe(0)
        ->and(GradeCalculator::bandIndex($standard, 10000, 100))->toBe(4);
});

it('works out the percentage of an exam that is not out of 100', function () use ($standard) {
    // Out of 50: 40 is exactly 80 percent, 39.99 is just below.
    expect(GradeCalculator::bandIndex($standard, 4000, 50))->toBe(4)
        ->and(GradeCalculator::bandIndex($standard, 3999, 50))->toBe(3)
        ->and(GradeCalculator::bandIndex($standard, 2500, 50))->toBe(1)
        ->and(GradeCalculator::bandIndex($standard, 2499, 50))->toBe(0)
        // Out of 30: 24 is exactly 80 percent.
        ->and(GradeCalculator::bandIndex($standard, 2400, 30))->toBe(4)
        ->and(GradeCalculator::bandIndex($standard, 2399, 30))->toBe(3)
        // Out of 7: 5.6 is exactly 80 percent.
        ->and(GradeCalculator::bandIndex($standard, 560, 7))->toBe(4)
        ->and(GradeCalculator::bandIndex($standard, 559, 7))->toBe(3)
        // Out of 1000.
        ->and(GradeCalculator::bandIndex($standard, 80000, 1000))->toBe(4)
        ->and(GradeCalculator::bandIndex($standard, 79999, 1000))->toBe(3);
});

it('handles fractional boundaries such as 44.5 percent', function () {
    $bands = [0, 4450, 7500];

    expect(GradeCalculator::bandIndex($bands, 4450, 100))->toBe(1)
        ->and(GradeCalculator::bandIndex($bands, 4449, 100))->toBe(0)
        ->and(GradeCalculator::bandIndex($bands, 7500, 100))->toBe(2);
});

it('gives no grade to an impossible score', function () use ($standard) {
    expect(GradeCalculator::bandIndex($standard, -1, 100))->toBeNull()
        ->and(GradeCalculator::bandIndex($standard, 10001, 100))->toBeNull()
        ->and(GradeCalculator::bandIndex($standard, 5100, 50))->toBeNull()
        ->and(GradeCalculator::bandIndex($standard, 500, 0))->toBeNull()
        ->and(GradeCalculator::bandIndex([], 500, 100))->toBeNull();
});

it('never gives a lower grade to a higher score', function (int $max) use ($standard) {
    $previous = 0;

    for ($score = 0; $score <= $max * 100; $score++) {
        $index = GradeCalculator::bandIndex($standard, $score, $max);

        expect($index)->not->toBeNull()->and($index)->toBeGreaterThanOrEqual($previous);
        $previous = $index;
    }
})->with([100, 50, 30, 20, 7, 1]);

it('agrees with an independent exact calculation for every score', function (int $max) use ($standard) {
    for ($score = 0; $score <= $max * 100; $score++) {
        // score/max >= minimum/100 (both sides in hundredths), multiplied out: score * 100 >= minimum * max.
        $expected = 0;
        foreach ($standard as $index => $minimum) {
            if ($score * 100 >= $minimum * $max) {
                $expected = $index;
            }
        }

        expect(GradeCalculator::bandIndex($standard, $score, $max))->toBe($expected);
    }
})->with([100, 50, 30, 7]);
