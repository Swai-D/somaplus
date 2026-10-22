{{-- Variables: scale (GradingScale or null). Ten lines are shown; empty lines are ignored. --}}
@php($editing = isset($scale))
@php($existing = $editing ? $scale->bands->values() : collect())
@include('academics._field', ['name' => 'name', 'label' => __('exams.scales.name'), 'value' => $scale->name ?? ''])
@error('bands')<div class="alert alert-danger">{{ $message }}</div>@enderror
<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>{{ __('exams.scales.min') }}</th>
                <th>{{ __('exams.scales.grade') }}</th>
                <th>{{ __('exams.scales.points') }}</th>
                <th>{{ __('exams.scales.remark') }}</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 10; $i++)
                @php($band = $existing->get($i))
                <tr>
                    <td><input class="form-control form-control-sm" type="text" inputmode="decimal" maxlength="8" name="bands[{{ $i }}][min_mark]" value="{{ old("bands.{$i}.min_mark", $band ? rtrim(rtrim($band->min_mark, '0'), '.') : '') }}"></td>
                    <td><input class="form-control form-control-sm" type="text" maxlength="5" name="bands[{{ $i }}][grade]" value="{{ old("bands.{$i}.grade", $band->grade ?? '') }}"></td>
                    <td><input class="form-control form-control-sm" type="number" min="0" max="20" name="bands[{{ $i }}][points]" value="{{ old("bands.{$i}.points", $band->points ?? '') }}"></td>
                    <td><input class="form-control form-control-sm" type="text" maxlength="100" name="bands[{{ $i }}][remark]" value="{{ old("bands.{$i}.remark", $band->remark ?? '') }}"></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
<p class="text-muted fs-13">{{ __('exams.scales.rule') }}</p>
