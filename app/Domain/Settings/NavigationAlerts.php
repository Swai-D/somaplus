<?php

namespace App\Domain\Settings;

use App\Enums\SubscriptionStatus;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Tenancy\TenantContext;

/**
 * The notices the bell of the top bar shows. Only real ones: the state of the school's
 * subscription, the exams of the current year that are still open and the invoices that
 * are not paid yet. A notice is only worked out for a person who may see the page it
 * talks about, and nothing about a pupil is in it.
 */
final class NavigationAlerts
{
    /**
     * @return list<array{title: string, message: string, url: string|null, icon: string, tone: string}>
     */
    public function handle(?User $user): array
    {
        $school = app(TenantContext::class)->current();

        if ($school === null) {
            return [];
        }

        $alerts = [];

        if ($school->subscription_status === SubscriptionStatus::ReadOnly) {
            $alerts[] = [
                'title' => __('layout.alert.read_only'),
                'message' => __('layout.alert.read_only_message'),
                'url' => null,
                'icon' => 'ti ti-lock',
                'tone' => 'danger',
            ];
        } elseif ($school->subscription_status === SubscriptionStatus::Grace) {
            $alerts[] = [
                'title' => __('layout.alert.grace'),
                'message' => __('layout.alert.grace_message', ['date' => $school->grace_ends_at?->toFormattedDateString() ?? '']),
                'url' => null,
                'icon' => 'ti ti-clock',
                'tone' => 'warning',
            ];
        }

        if ($user?->can('exams.view')) {
            $open = $this->openExams();

            if ($open > 0) {
                $alerts[] = [
                    'title' => __('layout.alert.open_exams'),
                    'message' => trans_choice('layout.alert.open_exams_message', $open, ['count' => $open]),
                    'url' => route('exams.index'),
                    'icon' => 'ti ti-file-certificate',
                    'tone' => 'info',
                ];
            }
        }

        if ($user?->can('fees.view')) {
            $unpaid = $this->unpaidInvoices();

            if ($unpaid > 0) {
                $alerts[] = [
                    'title' => __('layout.alert.unpaid_invoices'),
                    'message' => trans_choice('layout.alert.unpaid_invoices_message', $unpaid, ['count' => $unpaid]),
                    'url' => route('fees.invoices.index'),
                    'icon' => 'ti ti-receipt',
                    'tone' => 'warning',
                ];
            }
        }

        return $alerts;
    }

    /** The exams of the current year that nobody has locked yet. */
    private function openExams(): int
    {
        $year = AcademicYear::query()->where('is_current', true)->first();

        if ($year === null) {
            return 0;
        }

        return Exam::query()
            ->where('academic_year_id', $year->id)
            ->whereNull('locked_at')
            ->count();
    }

    /**
     * The invoices that are not paid in full. A payment that was reversed does not count,
     * and the money is never added across currencies: the invoices of one school are in its
     * own currency.
     */
    private function unpaidInvoices(): int
    {
        $paid = Payment::query()
            ->selectRaw('coalesce(sum(payments.amount), 0)')
            ->whereColumn('payments.invoice_id', 'invoices.id')
            ->whereDoesntHave('reversal');

        return Invoice::query()
            ->where('total', '>', $paid)
            ->count();
    }
}
