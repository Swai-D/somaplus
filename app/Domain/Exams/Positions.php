<?php

namespace App\Domain\Exams;

/**
 * Class positions from averages, exactly. Two averages total/count are compared by cross multiplication
 * (a.total * b.count against b.total * a.count), so nothing is rounded or divided and equal averages
 * always tie. Ties share a position and the next position is skipped (1, 1, 3).
 */
final class Positions
{
    /**
     * @param  array<int, array{total: int, count: int}>  $entries  keyed by student id; entries with count 0 get no position
     * @return array<int, int> student id => position
     */
    public static function rank(array $entries): array
    {
        $ranked = array_filter($entries, fn (array $e): bool => $e['count'] > 0);

        $positions = [];
        foreach ($ranked as $id => $entry) {
            $ahead = 0;
            foreach ($ranked as $other) {
                // other is strictly higher: other.total / other.count > entry.total / entry.count
                if ($other['total'] * $entry['count'] > $entry['total'] * $other['count']) {
                    $ahead++;
                }
            }
            $positions[$id] = $ahead + 1;
        }

        return $positions;
    }
}
