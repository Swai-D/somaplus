@extends('layouts.app')

@section('title', __('menu.integrations'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('menu.integrations') }}</h3>
        <p class="text-muted mb-0">{{ __('integrations.intro') }}</p>
    </div>

    @foreach ($generated as $name => $value)
        <div class="alert alert-warning" role="alert">
            <div class="fw-medium mb-1">{{ __('integrations.generated_title') }}</div>
            <code class="user-select-all">{{ $value }}</code>
            <div class="small mt-1">{{ __('integrations.generated_hint') }}</div>
        </div>
    @endforeach

    @forelse ($channels as $channel => $info)
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">{{ __('integrations.channels.'.$channel.'.title') }}</h5>
                <p class="text-muted">{{ __('integrations.channels.'.$channel.'.hint') }}</p>

                <form method="POST" action="{{ route('settings.integrations.update', $channel) }}" class="js-integration">
                    @csrf
                    @method('PUT')
                    @error('driver')<div class="alert alert-danger">{{ $message }}</div>@enderror

                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="enabled" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="enabled-{{ $channel }}" name="enabled" value="1" @checked(old('enabled', $info['enabled']))>
                        <label class="form-check-label" for="enabled-{{ $channel }}">{{ __('integrations.enabled') }}</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="driver-{{ $channel }}">{{ __('integrations.provider') }}</label>
                        <select class="form-select js-driver" id="driver-{{ $channel }}" name="driver">
                            <option value=""></option>
                            @foreach ($info['drivers'] as $key => $driver)
                                <option value="{{ $key }}" @selected(old('driver', $info['driver']) === $key)>{{ __($driver['label']) }}</option>
                            @endforeach
                        </select>
                    </div>

                    @foreach ($info['drivers'] as $key => $driver)
                        <div class="js-driver-fields row" data-driver="{{ $key }}" style="display: none;">
                            @foreach ($driver['fields'] as $field)
                                @php
                                    $name = $field['name'];
                                    $isSecret = $field['secret'] ?? false;
                                    $value = $driver['values'][$name] ?? ($field['default'] ?? '');
                                @endphp
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">{{ __($field['label']) }}@if ($field['required'] ?? false)<span class="text-danger ms-1">*</span>@endif</label>
                                    @if ($field['type'] === 'select')
                                        <select class="form-select" name="config[{{ $name }}]" disabled>
                                            @foreach ($field['options'] as $optionValue => $optionLabel)
                                                <option value="{{ $optionValue }}" @selected((string) old("config.{$name}", $value) === (string) $optionValue)>{{ __($optionLabel) }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($field['type'] === 'textarea')
                                        <textarea class="form-control font-monospace fs-13 @error("config.{$name}") is-invalid @enderror" name="config[{{ $name }}]" rows="5" disabled autocomplete="off"
                                            @if ($isSecret) placeholder="{{ in_array($name, $driver['savedSecrets'], true) ? __('integrations.secret_saved') : '' }}">@else>{{ old("config.{$name}", $value) }}@endif</textarea>
                                    @else
                                        <input class="form-control @error("config.{$name}") is-invalid @enderror" name="config[{{ $name }}]" disabled autocomplete="off"
                                            type="{{ $isSecret || $field['type'] === 'password' ? 'password' : 'text' }}"
                                            @if ($isSecret) value="" autocomplete="new-password" placeholder="{{ in_array($name, $driver['savedSecrets'], true) ? __('integrations.secret_saved') : (($field['generate'] ?? false) ? __('integrations.secret_generate') : '') }}"
                                            @else value="{{ old("config.{$name}", $value) }}" @endif>
                                    @endif
                                    @error("config.{$name}")<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    @if ($channel === 'payments' && $webhookUrl)
                        <div class="alert alert-info js-driver-note" data-driver="signed_webhook" style="display: none;">
                            <div class="fw-medium mb-1">{{ __('integrations.webhook.title') }}</div>
                            <code class="user-select-all">{{ $webhookUrl }}</code>
                            <div class="small mt-2">{{ __('integrations.webhook.how') }}</div>
                            <pre class="small mb-0 mt-2">{{ __('integrations.webhook.example') }}</pre>
                        </div>
                    @endif

                    @if ($channel === 'payments' && $gepgUrl)
                        <div class="alert alert-info js-driver-note" data-driver="gepg" style="display: none;">
                            <div class="fw-medium mb-1">{{ __('integrations.gepg.title') }}</div>
                            <code class="user-select-all">{{ $gepgUrl }}</code>
                            <div class="small mt-2">{{ __('integrations.gepg.how') }}</div>
                        </div>
                    @endif

                    @if ($channel === 'payments' && $selcomUrl)
                        <div class="alert alert-info js-driver-note" data-driver="selcom" style="display: none;">
                            <div class="fw-medium mb-1">{{ __('integrations.selcom.title') }}</div>
                            @foreach (['lookup', 'validation', 'notification'] as $step)
                                <div><code class="user-select-all">{{ str_replace('STEP', $step, $selcomUrl) }}</code></div>
                            @endforeach
                            <div class="small mt-2">{{ __('integrations.selcom.how') }}</div>
                        </div>
                    @endif

                    @if ($channel === 'payments' && $azampayUrl)
                        <div class="alert alert-info js-driver-note" data-driver="azampay" style="display: none;">
                            <div class="fw-medium mb-1">{{ __('integrations.azampay.title') }}</div>
                            <code class="user-select-all">{{ $azampayUrl }}</code>
                            <div class="small mt-2">{{ __('integrations.azampay.how') }}</div>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">{{ __('integrations.save') }}</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-muted">{{ __('integrations.none') }}</div></div>
    @endforelse

    @if (isset($channels['payments']))
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">{{ __('integrations.notices.title') }}</h5>
                <p class="text-muted">{{ __('integrations.notices.hint') }}</p>
                @if ($notifications->isEmpty())
                    <p class="text-muted mb-0">{{ __('integrations.notices.none') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr>
                                <th>{{ __('integrations.notices.received') }}</th><th>{{ __('fees.invoices.control_number') }}</th><th>{{ __('integrations.notices.reference') }}</th><th class="text-end">{{ __('fees.structures.amount') }}</th><th>{{ __('fees.payments.status') }}</th>
                            </tr></thead>
                            <tbody>
                                @foreach ($notifications as $notice)
                                    <tr>
                                        <td>{{ $notice->received_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $notice->control_number }}</td>
                                        <td>{{ $notice->provider_reference }}</td>
                                        <td class="text-end">@if ($notice->amount !== null && $notice->currency !== null){{ App\Support\Money::of($notice->amount, $notice->currency)->format() }}@endif</td>
                                        <td>{{ __('integrations.notices.statuses.'.$notice->status) }}@if ($notice->reason) <span class="text-muted fs-13">· {{ __('integrations.notices.reasons.'.$notice->reason) }}</span>@endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('page-scripts')
<script>
    document.querySelectorAll('.js-integration').forEach(function (form) {
        var select = form.querySelector('.js-driver');

        function sync() {
            form.querySelectorAll('.js-driver-fields').forEach(function (block) {
                var active = block.dataset.driver === select.value;
                block.style.display = active ? '' : 'none';
                // Only the chosen provider's fields are sent.
                block.querySelectorAll('input, select').forEach(function (input) { input.disabled = !active; });
            });
            form.querySelectorAll('.js-driver-note').forEach(function (note) {
                note.style.display = note.dataset.driver === select.value ? '' : 'none';
            });
        }

        select.addEventListener('change', sync);
        sync();
    });
</script>
@endpush
