<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\AdmissionSetting;
use App\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;

/**
 * Saves how the school makes admission numbers. The optional "start at" only moves the running number
 * forward (never back), so a number already given out can never be given again.
 */
final class SaveAdmissionSettings
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{mode: string, prefix?: string|null, separator?: string|null, year_format?: string|null, digits?: int|string|null, start_at?: int|string|null}  $data
     */
    public function handle(array $data): AdmissionSetting
    {
        return DB::transaction(function () use ($data): AdmissionSetting {
            $setting = AdmissionSetting::query()->first() ?? new AdmissionSetting;

            $setting->fill([
                'mode' => $data['mode'],
                'prefix' => strtoupper(trim((string) ($data['prefix'] ?? ''))),
                'separator' => ($data['separator'] ?? 'none') === 'none' ? '' : (string) $data['separator'],
                'year_format' => $data['year_format'] ?? AdmissionSetting::YEAR_NONE,
                'digits' => (int) ($data['digits'] ?? 4),
            ])->save();

            $start = (int) ($data['start_at'] ?? 0);
            if ($setting->isAuto() && $start > 1) {
                $type = AdmissionNumbers::sequenceType($setting, now()->year);
                $sequence = DocumentSequence::query()->where('type', $type)->lockForUpdate()->first();

                if ($sequence === null) {
                    DocumentSequence::query()->create(['type' => $type, 'last_number' => $start - 1]);
                } elseif ($sequence->last_number < $start - 1) {
                    $sequence->last_number = $start - 1;
                    $sequence->save();
                }
            }

            $this->audit->handle('settings.admission_updated', $setting);

            return $setting;
        });
    }
}
