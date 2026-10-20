{{-- A read-only list of bands. Variables: bands. --}}
<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr>
            <th>{{ __('exams.scales.min') }}</th><th>{{ __('exams.scales.grade') }}</th><th>{{ __('exams.scales.points') }}</th><th>{{ __('exams.scales.remark') }}</th>
        </tr></thead>
        <tbody>
            @foreach ($bands->sortByDesc(fn ($band) => (float) $band->min_mark) as $band)
                <tr>
                    <td>{{ rtrim(rtrim($band->min_mark, '0'), '.') }}%</td>
                    <td class="fw-medium">{{ $band->grade }}</td>
                    <td>{{ $band->points }}</td>
                    <td>{{ $band->remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
