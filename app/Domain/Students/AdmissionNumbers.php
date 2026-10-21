<?php

namespace App\Domain\Students;

use App\Domain\Fees\NextNumber;
use App\Models\AdmissionSetting;
use App\Models\DocumentSequence;
use App\Models\Student;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

/**
 * Makes admission numbers from the school's format, for example SCH-2026-0001.
 *
 * The running number comes from the school's document sequence, so two people saving at the same time never
 * get the same one. A format with a year counts again from 1 every year (one sequence per year). A number
 * that a student already has (typed earlier, or imported from an old register) is skipped, never reused.
 * Call it inside the transaction that saves the student.
 */
final class AdmissionNumbers
{
    private const MAX_TRIES = 1000;

    public function __construct(private readonly NextNumber $next) {}

    /** The number for a running number, in the school's format. Also used to show examples. */
    public static function render(AdmissionSetting $setting, int $year, int $sequence): string
    {
        $parts = [];
        if ($setting->prefix !== '') {
            $parts[] = $setting->prefix;
        }
        if ($setting->year_format === AdmissionSetting::YEAR_FULL) {
            $parts[] = (string) $year;
        } elseif ($setting->year_format === AdmissionSetting::YEAR_SHORT) {
            $parts[] = substr((string) $year, -2);
        }
        $parts[] = str_pad((string) $sequence, max(1, $setting->digits), '0', STR_PAD_LEFT);

        return implode($setting->separator, $parts);
    }

    /** One sequence per year when the format holds the year, one for ever when it does not. */
    public static function sequenceType(AdmissionSetting $setting, int $year): string
    {
        return $setting->year_format === AdmissionSetting::YEAR_NONE ? 'admission' : 'admission:'.$year;
    }

    /**
     * @param  list<string>  $reserved  numbers a later row of the same file will bring with it
     */
    public function generate(CarbonInterface $admittedOn, array $reserved = []): string
    {
        $setting = AdmissionSetting::current();
        $type = self::sequenceType($setting, $admittedOn->year);

        for ($try = 0; $try < self::MAX_TRIES; $try++) {
            $number = self::render($setting, $admittedOn->year, $this->next->handle($type));

            if (! in_array($number, $reserved, true) && ! Student::query()->where('admission_number', $number)->exists()) {
                return $number;
            }
        }

        throw ValidationException::withMessages(['admission_number' => __('admission.exhausted')]);
    }

    /** The number the next student would get, without taking it. */
    public static function peek(CarbonInterface $admittedOn): string
    {
        $setting = AdmissionSetting::current();
        $last = (int) DocumentSequence::query()->where('type', self::sequenceType($setting, $admittedOn->year))->value('last_number');

        return self::render($setting, $admittedOn->year, $last + 1);
    }
}
