<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain\Integrations\Drivers\Payments\SelcomDriver;
use App\Domain\Integrations\Payments\ReceivePaymentNotification;
use App\Domain\Integrations\Services\IntegrationManager;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Support\Money;
use App\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Selcom C2B collection calls (lookup, validation, notification). Public on purpose: the school is named in the path
 * and every call needs the school's bearer token (compared in constant time). Selcom always pays in TZS and names the
 * invoice by its control number (`utilityref`). Wrong host, unknown school, switched-off integration or another
 * provider answer 404; a missing or wrong token 401; everything else answers 200 with Selcom's result code.
 *
 * Codes: 000 success, 010 unknown control number, 012 bad amount, 400 any other failure.
 */
class SelcomWebhookController extends Controller
{
    private const CURRENCY = 'TZS';

    public function __invoke(Request $request, string $tenant, string $step, TenantContext $context, ReceivePaymentNotification $receive): JsonResponse
    {
        abort_unless(in_array(strtolower($request->getHost()), (array) config('tenancy.central_domains', []), true), 404);

        $school = Tenant::query()->whereKey($tenant)->first();
        abort_if($school === null, 404);

        return $context->run($school, function () use ($request, $step, $receive): JsonResponse {
            $active = app(IntegrationManager::class)->active('payments');
            if ($active === null || $active['driver'] !== SelcomDriver::KEY) {
                abort(404);
            }

            $token = (string) ($active['secrets']['bearer_token'] ?? '');
            $given = (string) preg_replace('/^Bearer\s+/i', '', (string) $request->header('Authorization'));
            if ($token === '' || ! hash_equals($token, $given)) {
                return response()->json(['resultcode' => '401', 'result' => 'FAIL', 'message' => 'Unauthorized'], 401);
            }

            $body = $request->json()->all();
            $reference = is_string($body['reference'] ?? null) ? $body['reference'] : '';
            $control = is_string($body['utilityref'] ?? null) ? trim($body['utilityref']) : '';
            $invoice = preg_match('/^\d{8,20}$/', $control) === 1 ? Invoice::query()->with('student')->where('control_number', $control)->first() : null;

            if ($invoice === null || $invoice->currency !== self::CURRENCY) {
                return $this->answer($reference, '010', 'FAIL', 'Invalid payment reference');
            }

            if ($step === 'lookup') {
                return $this->answer($reference, '000', 'SUCCESS', 'Found', ['name' => $invoice->student->fullName()]);
            }

            $amount = $body['amount'] ?? null;
            $amountText = is_int($amount) ? (string) $amount : (is_string($amount) ? trim($amount) : '');
            $minor = Money::parse($amountText, self::CURRENCY);
            if ($minor === null || $minor <= 0) {
                return $this->answer($reference, '012', 'FAIL', 'Invalid amount');
            }

            if ($step === 'validation') {
                return $this->answer($reference, '000', 'SUCCESS', 'Valid');
            }

            if ($reference === '' || mb_strlen($reference) > 90) {
                return $this->answer($reference, '400', 'FAIL', 'Missing reference');
            }

            $outcome = $receive->handle([
                'control_number' => $control,
                'amount' => $amountText,
                'currency' => self::CURRENCY,
                'reference' => 'SELCOM-'.$reference,
                'method' => 'mobile_money',
            ]);

            return in_array($outcome, [ReceivePaymentNotification::APPLIED, ReceivePaymentNotification::DUPLICATE], true)
                ? $this->answer($reference, '000', 'SUCCESS', 'Recorded')
                : $this->answer($reference, $outcome === ReceivePaymentNotification::UNMATCHED ? '010' : '400', 'FAIL', 'Not recorded');
        });
    }

    /**
     * @param  array<string, string>  $extra
     */
    private function answer(string $reference, string $code, string $result, string $message, array $extra = []): JsonResponse
    {
        return response()->json(['reference' => $reference, 'resultcode' => $code, 'result' => $result, 'message' => $message] + $extra);
    }
}
