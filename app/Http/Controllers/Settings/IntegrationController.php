<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Integrations\Actions\SaveIntegration;
use App\Domain\Integrations\Services\IntegrationRegistry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\IntegrationRequest;
use App\Models\PaymentNotification;
use App\Models\TenantIntegration;
use App\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    public function index(IntegrationRegistry $registry): View
    {
        Gate::authorize('manage', TenantIntegration::class);

        return $this->page($registry, []);
    }

    public function update(IntegrationRequest $request, SaveIntegration $save, IntegrationRegistry $registry): RedirectResponse|View
    {
        /** @var array{enabled: bool|int|string, driver?: string|null, config?: array<string, mixed>|null} $data */
        $data = $request->validated();
        $generated = $save->handle($request->channel(), $data);

        if ($generated === []) {
            return redirect()->route('settings.integrations.index')->with('status', __('integrations.saved'));
        }

        // A secret Somaplus made is shown once, in this response only: it is never put in the session or stored in plain text.
        return $this->page($registry, $generated);
    }

    /**
     * @param  array<string, string>  $generated
     */
    private function page(IntegrationRegistry $registry, array $generated): View
    {
        $saved = TenantIntegration::query()->get()->keyBy('channel');
        $channels = [];

        foreach ($registry->channels() as $channel) {
            $row = $saved->get($channel);
            $drivers = [];

            foreach ($registry->drivers($channel) as $key => $class) {
                $drivers[$key] = [
                    'label' => $class::label(),
                    'fields' => $class::fields(),
                    // Only the saved provider's values are shown, and secrets never leave the server.
                    'values' => $row?->driver === $key ? ($row->settings ?? []) : [],
                    'savedSecrets' => $row?->driver === $key ? array_keys($row->secrets ?? []) : [],
                ];
            }

            $channels[$channel] = ['enabled' => (bool) $row?->enabled, 'driver' => $row?->driver, 'drivers' => $drivers];
        }

        $tenantId = app(TenantContext::class)->currentId();

        return view('settings.integrations', [
            'channels' => $channels,
            'generated' => $generated,
            'webhookUrl' => $tenantId === null ? null : rtrim((string) config('app.url'), '/').route('webhooks.payments', [$tenantId], false),
            'gepgUrl' => $tenantId === null ? null : rtrim((string) config('app.url'), '/').route('webhooks.gepg', [$tenantId], false),
            'selcomUrl' => $tenantId === null ? null : rtrim((string) config('app.url'), '/').route('webhooks.selcom', [$tenantId, 'STEP'], false),
            'azampayUrl' => $tenantId === null ? null : rtrim((string) config('app.url'), '/').route('webhooks.azampay', [$tenantId], false),
            'notifications' => PaymentNotification::query()->orderByDesc('id')->limit(20)->get(),
        ]);
    }
}
