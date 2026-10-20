{{-- Amount, method, date and reference of a payment. Variables: key (idempotency key), withControlNumber (optional). --}}
<input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $key) }}">
@error('idempotency_key')<div class="alert alert-danger">{{ $message }}</div>@enderror
<div class="row">
    @if ($withControlNumber ?? false)
        <div class="col-md-6">@include('academics._field', ['name' => 'control_number', 'label' => __('fees.invoices.control_number'), 'value' => '', 'hint' => __('fees.payments.control_hint')])</div>
    @endif
    <div class="col-md-6">@include('academics._field', ['name' => 'amount', 'label' => __('fees.structures.amount'), 'value' => ''])</div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="method">{{ __('fees.payments.method') }}<span class="text-danger"> *</span></label>
        <select class="form-select @error('method') is-invalid @enderror" id="method" name="method" required>
            @foreach (App\Models\Payment::METHODS as $method)
                <option value="{{ $method }}" @selected(old('method') === $method)>{{ __('fees.payments.methods.'.$method) }}</option>
            @endforeach
        </select>
        @error('method')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">@include('academics._field', ['name' => 'paid_on', 'type' => 'date', 'label' => __('fees.payments.date'), 'value' => now()->toDateString()])</div>
    <div class="col-md-6">@include('academics._field', ['name' => 'reference', 'label' => __('fees.payments.reference'), 'value' => '', 'required' => false, 'hint' => __('fees.payments.reference_hint')])</div>
</div>
