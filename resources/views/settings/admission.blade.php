@extends('layouts.app')

@section('title', __('admission.title'))

@php
    // The values the page was saved with, for the live preview. The array is built here because
    // @json() splits its argument on commas, so an array literal inside it would not compile.
    $saved = [
        'prefix' => $setting->prefix,
        'year_format' => $setting->year_format,
        'separator' => $setting->separator === '' ? 'none' : $setting->separator,
        'digits' => $setting->digits,
    ];
@endphp

@push('styles')
    <style>
        .adm-mode{display:block;border:2px solid #E6EAED;border-radius:12px;padding:1rem 1.25rem;cursor:pointer;height:100%;transition:border-color .15s,box-shadow .15s}
        .adm-mode:hover{border-color:#FE9F43}
        .adm-mode input{position:absolute;opacity:0;pointer-events:none}
        .adm-mode:has(input:checked){border-color:#FE9F43;box-shadow:0 0 0 3px rgba(254,159,67,.18)}
        .adm-mode:has(input:focus-visible){outline:2px solid #FE9F43;outline-offset:2px}
        .adm-preview{background:#1f2937;color:#fff;border-radius:12px;padding:1.25rem 1.5rem}
        .adm-preview .num{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:2rem;letter-spacing:.04em;word-break:break-all}
        .adm-preview .ex{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;opacity:.75}
    </style>
@endpush

@section('page')
    @php($manual = old('mode', $setting->mode) !== 'auto')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('admission.title') }}</h3>
        <p class="text-muted mb-0">{{ __('admission.intro') }}</p>
    </div>

    <form method="POST" action="{{ route('settings.admission.update') }}" id="admission-form">
        @csrf
        @method('PUT')
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <div class="row g-3 mb-3">
            @foreach (['manual', 'auto'] as $mode)
                <div class="col-md-6 position-relative">
                    <label class="adm-mode">
                        <input type="radio" name="mode" value="{{ $mode }}" @checked(old('mode', $setting->mode) === $mode)>
                        <span class="d-block fw-semibold mb-1"><i class="ti {{ $mode === 'auto' ? 'ti-wand' : 'ti-pencil' }} me-1"></i>{{ __('admission.mode.'.$mode) }}</span>
                        <span class="d-block text-muted fs-13">{{ __('admission.mode.'.$mode.'_hint') }}</span>
                    </label>
                </div>
            @endforeach
        </div>

        <div class="card" id="adm-builder">
            <div class="card-header"><h5 class="mb-0">{{ __('admission.format') }}</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="prefix">{{ __('admission.prefix') }}</label>
                        <input class="form-control @error('prefix') is-invalid @enderror" id="prefix" name="prefix" maxlength="10" value="{{ old('prefix', $setting->prefix) }}" autocomplete="off" placeholder="SCH">
                        <div class="form-text">{{ __('admission.prefix_hint') }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="year_format">{{ __('admission.year') }}</label>
                        <select class="form-select" id="year_format" name="year_format">
                            @foreach (['YYYY', 'YY', 'none'] as $option)
                                <option value="{{ $option }}" @selected(old('year_format', $setting->year_format) === $option)>{{ __('admission.years.'.$option) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="separator">{{ __('admission.separator') }}</label>
                        <select class="form-select" id="separator" name="separator">
                            @foreach (['-', '/', '.', '_', 'none'] as $option)
                                <option value="{{ $option }}" @selected(old('separator', $setting->separator === '' ? 'none' : $setting->separator) === $option)>{{ __('admission.separators.'.$option) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="digits">{{ __('admission.digits') }}</label>
                        <select class="form-select" id="digits" name="digits">
                            @foreach (range(2, 8) as $option)
                                <option value="{{ $option }}" @selected((int) old('digits', $setting->digits) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ __('admission.digits_hint') }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="start_at">{{ __('admission.start_at') }}</label>
                        <input class="form-control @error('start_at') is-invalid @enderror" id="start_at" name="start_at" type="number" min="1" value="{{ old('start_at') }}" placeholder="1">
                        <div class="form-text">{{ __('admission.start_at_hint') }}</div>
                    </div>
                </div>

                <div class="adm-preview mt-2">
                    <div class="small text-uppercase opacity-75">{{ __('admission.preview_next') }}</div>
                    <div class="num" id="adm-next" aria-live="polite">{{ $next }}</div>
                    <div class="small mt-2 opacity-75">{{ __('admission.preview_examples') }}: <span class="ex" id="adm-examples"></span></div>
                    <div class="small mt-1 opacity-75" id="adm-year-note">{{ __('admission.year_note') }}</div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('admission.save') }}</button>
    </form>

@endsection

@push('page-scripts')
    <script>
        (function () {
            var form = document.getElementById('admission-form');
            var year = {{ $year }};
            var peek = @json($next);
            var saved = @json($saved);

            function value(name) { return form.elements[name].value; }
            function render(seq) {
                var parts = [], prefix = value('prefix').trim().toUpperCase(), yf = value('year_format');
                if (prefix) parts.push(prefix);
                if (yf === 'YYYY') parts.push(String(year));
                if (yf === 'YY') parts.push(String(year).slice(-2));
                var s = String(seq); while (s.length < parseInt(value('digits'), 10)) s = '0' + s;
                parts.push(s);
                return parts.join(value('separator') === 'none' ? '' : value('separator'));
            }
            function refresh() {
                var auto = form.elements['mode'].value === 'auto';
                document.getElementById('adm-builder').style.display = auto ? '' : 'none';
                var start = parseInt(value('start_at'), 10);
                var unchanged = ['prefix', 'year_format', 'separator', 'digits'].every(function (k) { return String(saved[k]) === String(value(k)).trim().toUpperCase() || String(saved[k]) === String(value(k)); });
                var first = start > 1 ? start : 1;
                document.getElementById('adm-next').textContent = (unchanged && !(start > 1)) ? peek : render(first);
                document.getElementById('adm-examples').textContent = [first, first + 1, first + 2].map(render).join(' · ');
                document.getElementById('adm-year-note').style.display = value('year_format') === 'none' ? 'none' : '';
            }
            form.addEventListener('input', refresh);
            form.addEventListener('change', refresh);
            refresh();
        })();
    </script>
@endpush

