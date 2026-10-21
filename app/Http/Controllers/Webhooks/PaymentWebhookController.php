<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain\Integrations\Drivers\Payments\SignedWebhookDriver;
use App\Domain\Integrations\Payments\ReceivePaymentNotification;
use App\Domain\Integrations\Payments\WebhookSignature;
use App\Domain\Integrations\Services\IntegrationManager;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Payment notices from a provider. Public on purpose: the school is named in the address, and the body is only
 * believed after its HMAC signature (with the school's own secret) and timestamp check out, so a forged or
 * replayed call changes nothing. It only answers on the central address, and never reveals whether a school
 * exists or has the integration switched on (both answer 404). It is not behind `tenant.writable`: money that
 * really moved is recorded even for a school that has become read-only.
 */
class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, string $tenant, TenantContext $context, ReceivePaymentNotification $receive): JsonResponse
    {
        abort_unless(in_array(strtolower($request->getHost()), (array) config('tenancy.central_domains', []), true), 404);

        $school = Tenant::query()->whereKey($tenant)->first();
        abort_if($school === null, 404);

        return $context->run($school, function () use ($request, $receive): JsonResponse {
            $active = app(IntegrationManager::class)->active('payments');
            if ($active === null || $active['driver'] !== SignedWebhookDriver::KEY) {
                abort(404);
            }

            $valid = WebhookSignature::valid(
                (string) ($active['secrets']['webhook_secret'] ?? ''),
                $request->header('X-Somaplus-Timestamp'),
                $request->header('X-Somaplus-Signature'),
                $request->getContent(),
            );
            if (! $valid) {
                return response()->json(['status' => 'invalid_signature'], 401);
            }

            $payload = json_decode($request->getContent(), true);
            $outcome = is_array($payload) ? $receive->handle($payload) : ReceivePaymentNotification::INVALID;

            return response()->json(['status' => $outcome], $outcome === ReceivePaymentNotification::INVALID ? 422 : 200);
        });
    }
}
