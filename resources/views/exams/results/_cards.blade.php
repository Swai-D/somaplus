    @if (count($exams) === 0)
        <div class="card"><div class="card-body text-muted">{{ __('exams.report.empty') }}</div></div>
    @endif

    @foreach ($exams as $card)
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">{{ $card->exam->name }}</h5>
                <p class="text-muted fs-13">{{ $card->class->name }} · {{ __('exams.exams.max_mark') }}: {{ $card->exam->max_mark }}</p>
                <div class="table-responsive">
                    <table class="table align-middle mb-3">
                        <thead><tr>
                            <th>{{ __('exams.marks.subject') }}</th><th>{{ __('exams.marks.score') }}</th><th>{{ __('exams.marks.grade') }}</th><th>{{ __('exams.scales.remark') }}</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($card->lines as $line)
                                <tr>
                                    <td>{{ $line->subject->name }}</td>
                                    <td>@if ($line->absent){{ __('exams.marks.absent') }}@elseif ($line->score !== null){{ $line->scoreText() }}@else<span class="text-muted">–</span>@endif</td>
                                    <td class="fw-medium">{{ $line->band?->grade }}</td>
                                    <td class="text-muted">{{ $line->band?->remark }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @php($r = $card->result)
                @if ($r->hasScores())
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('exams.results.total') }}</dt><dd class="col-sm-8">{{ $r->totalText() }}</dd>
                        <dt class="col-sm-4">{{ __('exams.results.average') }}</dt><dd class="col-sm-8">{{ $r->averageText() }}</dd>
                        <dt class="col-sm-4">{{ __('exams.report.overall_grade') }}</dt><dd class="col-sm-8 fw-medium">{{ $r->band?->grade }} <span class="text-muted fs-13">{{ $r->band?->remark }}</span></dd>
                        <dt class="col-sm-4">{{ __('exams.results.position') }}</dt><dd class="col-sm-8">{{ $r->classPosition }} / {{ $r->classSize }}</dd>
                        @if ($r->streamPosition !== null)
                            <dt class="col-sm-4">{{ __('exams.results.stream_position') }}</dt><dd class="col-sm-8">{{ $r->streamPosition }} / {{ $r->streamSize }}</dd>
                        @endif
                    </dl>
                @endif
            </div>
        </div>
    @endforeach
