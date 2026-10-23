@extends('layouts.app')

@section('title', __('menu.events'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.events') }}</h3>
            <p class="text-muted mb-0">{{ __('communication.events.intro') }}</p>
        </div>
        @can('create', App\Models\Event::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#event-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('communication.events.add') }}
            </button>
        @endcan
    </div>

    <div class="row gy-4">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <a href="{{ route('communication.events.index', ['month' => $prevMonth]) }}" class="btn btn-sm btn-outline-secondary" aria-label="{{ __('communication.events.back') }}"><i class="ti ti-chevron-left"></i></a>
                    <h6 class="fw-semibold mb-0">{{ $month->translatedFormat('F Y') }}</h6>
                    <a href="{{ route('communication.events.index', ['month' => $nextMonth]) }}" class="btn btn-sm btn-outline-secondary" aria-label="{{ __('communication.events.back') }}"><i class="ti ti-chevron-right"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0 calendar">
                            <thead>
                                <tr class="text-muted">
                                    @foreach ([__('app.days.sun'), __('app.days.mon'), __('app.days.tue'), __('app.days.wed'), __('app.days.thu'), __('app.days.fri'), __('app.days.sat')] as $day)
                                        <th class="fw-semibold">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weeks as $week)
                                    <tr>
                                        @foreach ($week as $day)
                                            <td class="py-2 {{ $day->month !== $month->month ? 'text-muted bg-light' : '' }}">
                                                <span class="badge {{ $day->isSameDay(now()) ? 'bg-primary' : 'bg-transparent text-body' }}">{{ $day->day }}</span>
                                                @if ($byDay->has($day->format('Y-m-d')))
                                                    <div class="d-grid gap-1 px-1">
                                                        @foreach ($byDay->get($day->format('Y-m-d'))->take(3) as $dayEvent)
                                                            <a href="{{ route('communication.events.edit', $dayEvent) }}"
                                                                class="d-block text-truncate text-start fs-11 event-chip"
                                                                title="{{ $dayEvent->title }}">{{ $dayEvent->title }}</a>
                                                        @endforeach
                                                        @if ($byDay->get($day->format('Y-m-d'))->count() > 3)
                                                            <span class="text-muted fs-11">+{{ $byDay->get($day->format('Y-m-d'))->count() - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header"><h6 class="fw-semibold mb-0">{{ __('communication.events.upcoming') }}</h6></div>
                <div class="card-body">
                    @forelse ($upcoming as $event)
                        <div class="d-flex justify-content-between gap-2 py-2 {{ $loop->first ? '' : 'border-top' }}">
                            <div class="min-width-zero">
                                <a href="{{ route('communication.events.edit', $event) }}" class="fw-medium d-block text-truncate">{{ $event->title }}</a>
                                <span class="text-muted fs-13">{{ $event->starts_at->translatedFormat('D, j M g:i A') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('communication.events.upcoming_none') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @can('create', App\Models\Event::class)
        <x-form-modal id="event-modal" title="{{ __('communication.events.add') }}" action="{{ route('communication.events.store') }}">
            @include('communication.events._fields', [
                'event' => null,
                'prefill' => $month->format('Y-m-d').'T08:00',
            ])
        </x-form-modal>
    @endcan
@endsection
