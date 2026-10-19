<?php

namespace App\Domain\Settings;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Writes one line to the audit log: who did what to which sensitive record.
 * Ids only, never names, phone numbers or other personal data.
 */
final class RecordAudit
{
    public function handle(string $action, Model $subject): void
    {
        AuditLog::query()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
        ]);
    }
}
