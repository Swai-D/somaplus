<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain\Integrations\Drivers\Payments\GepgDriver;
use App\Domain\Integrations\Payments\Gepg\GepgMessage;
use App\Domain\Integrations\Payments\ReceivePaymentNotification;
use App\Domain\Integrations\Services\IntegrationManager;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * GePG payment callback (`gepgPmtSpInfo`). Public on purpose, like the signed webhook: the school is named in the
 * path and nothing is believed until the XML signature checks out with the school's saved GePG certificate and the
 * message names the school's own service provider code. Unknown school, switched-off or other-provider schools and
 * non-central hosts answer 404. Every payment in a verified notice is kept (recorded, or listed in Settings when no
 * invoice has the control number) and GePG then gets a signed 7101 acknowledgement, so it stops resending.
 */
class GepgWebhookController extends Controller
{
    public function __invoke(Request $request, string $tenant, TenantContext $context, ReceivePaymentNotification $receive): Response
    {
        abort_unless(in_array(strtolower($request->getHost()), (array) config('tenancy.central_domains', []), true), 404);

        $school = Tenant::query()->whereKey($tenant)->first();
        abort_if($school === null, 404);

        return $context->run($school, function () use ($request, $receive): Response {
            $active = app(IntegrationManager::class)->active('payments');
            if ($active === null || $active['driver'] !== GepgDriver::KEY) {
                abort(404);
            }

            $raw = $request->getContent();
            $element = GepgMessage::paymentElement($raw);
            if ($element === null || ! GepgMessage::valid($element, GepgMessage::signature($raw), (string) ($active['settings']['gepg_certificate'] ?? ''))) {
                return response('invalid signature', 401);
            }

            $parsed = GepgMessage::parse($element);
            $own = (string) ($active['settings']['sp_code'] ?? '');
            if ($parsed === null || array_filter($parsed['items'], fn (array $item): bool => $item['sp_code'] !== $own) !== []) {
                return response('invalid message', 422);
            }

            foreach ($parsed['items'] as $item) {
                unset($item['sp_code']);
                $receive->handle($item + ['method' => 'bank']);
            }

            $ack = GepgMessage::acknowledgement((string) ($active['secrets']['signing_key'] ?? ''));

            return $ack === null ? response('cannot acknowledge', 500) : response($ack, 200, ['Content-Type' => 'application/xml']);
        });
    }
}
