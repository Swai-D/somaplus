<?php

use App\Domain\Exams\GradeCalculator;
use App\Domain\Exams\Positions;

function entry(int $total, int $count): array
{
    return ['total' => $total, 'count' => $count];
}

it('ranks by average and lets equal averages share a position', function () {
    $positions = Positions::rank([
        1 => entry(17000, 2), // 85
        2 => entry(8500, 1),  // 85, same average from fewer subjects
        3 => entry(9000, 1),  // 90
        4 => entry(6000, 1),  // 60
    ]);

    expect($positions)->toBe([1 => 2, 2 => 2, 3 => 1, 4 => 4]);
});

it('compares averages exactly, never rounded', function () {
    // 79.995 (15999 / 2) is below 80.00 (8000 / 1) even though both display as 80.00 when rounded.
    $positions = Positions::rank([1 => entry(15999, 2), 2 => entry(8000, 1)]);

    expect($positions)->toBe([1 => 2, 2 => 1]);
});

it('gives no position to a student without a score', function () {
    $positions = Positions::rank([1 => entry(0, 0), 2 => entry(0, 1), 3 => entry(5000, 1)]);

    expect($positions)->toBe([2 => 2, 3 => 1])->and($positions)->not->toHaveKey(1);
});

it('gives a student who scored zero a position, last', function () {
    expect(Positions::rank([1 => entry(0, 3), 2 => entry(100, 3)]))->toBe([1 => 2, 2 => 1]);
});

it('handles one student and nobody', function () {
    expect(Positions::rank([7 => entry(4000, 2)]))->toBe([7 => 1])->and(Positions::rank([]))->toBe([]);
});

it('skips positions after a tie', function () {
    $positions = Positions::rank([1 => entry(5000, 1), 2 => entry(5000, 1), 3 => entry(5000, 1), 4 => entry(4999, 1)]);

    expect($positions)->toBe([1 => 1, 2 => 1, 3 => 1, 4 => 4]);
});

it('grades an average exactly from the total and the number of subjects', function (int $total, int $count, int $max, ?int $index) {
    // bands: 0 F, 50 D, 60 C, 70 B, 80 A as hundredths of a percent
    expect(GradeCalculator::averageBandIndex([0, 5000, 6000, 7000, 8000], $total, $count, $max))->toBe($index);
})->with([
    'exactly 80 is A' => [16000, 2, 100, 4],
    '79.995 is B, not rounded up' => [15999, 2, 100, 3],
    'max 50, average 40 is A' => [8000, 2, 50, 4],
    'max 50, average 39.99 is B' => [7998, 2, 50, 3],
    'three subjects 79.99 average is B' => [23997, 3, 100, 3],
    'zero is F' => [0, 4, 100, 0],
    'above the possible total is refused' => [20001, 2, 100, null],
    'no subjects is refused' => [0, 0, 100, null],
    'negative is refused' => [-1, 1, 100, null],
]);
