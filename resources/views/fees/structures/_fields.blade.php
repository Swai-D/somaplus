{{-- Variables: structure (FeeStructure or null), terms, classes. Eight item lines are shown; empty lines are ignored. --}}
@php($editing = isset($structure))
@php($currency = app(App\Tenancy\TenantContext::class)->current()?->currency ?? App\Support\Money::DEFAULT_CURRENCY)
@php($existing = $editing ? $structure->items->values() : collect())
@include('academics._field', ['name' => 'name', 'label' => __('fees.structures.name'), 'value' => $structure->name ?? '', 'hint' => __('fees.structures.name_hint')])
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="term_id">{{ __('fees.structures.term') }}<span class="text-danger"> *</span></label>
        <select class="form-select @error('term_id') is-invalid @enderror" id="term_id" name="term_id" required>
            <option value="">{{ __('fees.choose') }}</option>
            @foreach ($terms as $term)
                <option value="{{ $term->id }}" @selected((int) old('term_id', $structure->term_id ?? 0) === $term->id)>{{ $term->academicYear->name }} – {{ $term->name }}</option>
            @endforeach
        </select>
        @error('term_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="school_class_id">{{ __('fees.structures.class') }}<span class="text-danger"> *</span></label>
        <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id" required>
            <option value="">{{ __('fees.choose') }}</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected((int) old('school_class_id', $structure->school_class_id ?? 0) === $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
</div>
@error('items')<div class="alert alert-danger">{{ $message }}</div>@enderror
<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>{{ __('fees.structures.item') }}</th><th style="width: 220px">{{ __('fees.structures.amount') }} ({{ $currency }})</th></tr></thead>
        <tbody>
            @for ($i = 0; $i < 8; $i++)
                @php($item = $existing->get($i))
                <tr>
                    <td><input class="form-control form-control-sm" type="text" maxlength="100" name="items[{{ $i }}][name]" value="{{ old("items.{$i}.name", $item->name ?? '') }}"></td>
                    <td><input class="form-control form-control-sm" type="text" inputmode="decimal" maxlength="14" autocomplete="off" name="items[{{ $i }}][amount]" value="{{ old("items.{$i}.amount", $item ? App\Support\Money::toInput($item->amount, $currency) : '') }}"></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
<p class="text-muted fs-13">{{ __('fees.structures.rule') }}</p>
