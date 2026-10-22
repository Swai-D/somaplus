<?php

namespace App\Domain\Communication\Sms;

use App\Domain\Integrations\Drivers\Sms\NextSmsDriver;
use App\Domain\Integrations\Services\IntegrationManager;

/** The current school's SMS gateway, or null when the school has not switched SMS on. Runs inside the school's context. */
final class SchoolSms
{
    public function __construct(private readonly IntegrationManager $integrations) {}

    public function gateway(): ?SmsGateway
    {
        $active = $this->integrations->active('sms');

        if ($active === null || $active['driver'] !== NextSmsDriver::KEY) {
            return null;
        }

        return new NextSmsGateway(
            (string) ($active['settings']['username'] ?? ''),
            (string) ($active['secrets']['password'] ?? ''),
            (string) ($active['settings']['sender_id'] ?? ''),
        );
    }

    public function isActive(): bool
    {
        return $this->integrations->active('sms') !== null;
    }
}
