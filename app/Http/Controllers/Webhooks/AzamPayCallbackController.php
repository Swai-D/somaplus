<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain\Integrations\Drivers\Payments\AzamPayDriver;
use App\Domain\Integrations\Payments\AzamPay\HandleAzamPayCallback;
use App\Domain\Integrations\Services\IntegrationManager;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AzamPay's result callback. Public on purpose: the school is named in the path (each school gives AzamPay its own
 * address) and the body is believed only after its RSA signature is verified, so a forged call changes nothing.
 * Wrong host, unknown school, switched-off integration or another provider answer 404; a bad signature 401.
 */
class AzamPayCallbackController extends Controller
{
    public function __invoke(Request $request, string $tenant, TenantContext $context, HandleAzamPayCallback $handle): JsonResponse
    {
        abort_unless(in_array(strtolower($request->getHost()), (array) config('tenancy.central_domains', []), true), 404);

        $school = Tenant::query()->whereKey($tenant)->first();
        abort_if($school === null, 404);

        return $context->run($school, function () use ($request, $handle): JsonResponse {
            $active = app(IntegrationManager::class)->active('payments');
            if ($active === null || $active['driver'] !== AzamPayDriver::KEY) {
                abort(404);
            }

            $outcome = $handle->handle($request->json()->all());

            return response()->json(['status' => $outcome], $outcome === HandleAzamPayCallback::INVALID_SIGNATURE ? 401 : 200);
        });
    }
}
