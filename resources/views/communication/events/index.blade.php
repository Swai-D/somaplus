@extends('layouts.app')

@section('title', __('menu.events'))

@php
    use App\Models\Event;

    // One calm colour per event, from the palette of the template's calendar,
    // spread over the events so neighbours rarely share one.
    $palette = ['#FE9F43', '#0E9384', '#3538CD', '#092C4C', '#A700FF'];
    $colourFor = fn (Event $event): string => $palette[(int) $event->id % count($palette)];

    // The seven names of the week, in the order the grid is drawn (a week that
    // starts on a Sunday, as the controller lays it out).
    $weekdays = collect(__('app.days'))->values()->all();

    // "12 Nov 2026, 09:00 – 14:00": the day, the time when there is one, and the
    // end when it comes (the same day again as a time, another day in full).
    $when = function (Event $event): string {
        $day = $event->starts_at->translatedFormat('d M Y');
        if ($event->starts_at->format('H:i') !== '00:00') {
            $day .= ', '.$event->starts_at->format('H:i');
        }
        if ($event->ends_at !== null) {
            $day .= $event->ends_at->isSameDay($event->starts_at)
                ? ' – '.$event->ends_at->format('H:i')
                : ' – '.$event->ends_at->translatedFormat('d M Y, H:i');
        }

        return $day;
    };
@endphp

@section('page')
<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4>{{ __('menu.events') }}</h4>
            <h6>{{ __('menu.sections.communication') }}</h6>
        </div>
    </div>
    <ul class="table-top-head">
        <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li>
        <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li>
    </ul>
    @can('create', App\Models\Event::class)
        <div class="page-btn">
            <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#event-modal" data-bs-toggle="modal" href="javascript:void(0);">
                <i class="ti ti-circle-plus me-1"></i>{{ __('communication.events.add') }}
            </a>
        </div>
    @endcan
</div>

<p class="text-muted mt-2 mb-0">
    {{ __('communication.events.intro') }}
</p>

<div class="mt-4">
    <div class="row gy-4">
        {{-- What is coming next, at the side, like the template's event page. --}}
        <div class="col-xxl-3 col-lg-4">
            <div class="card h-100 p-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">{{ __('communication.events.upcoming') }}</h5>
                    @unless ($upcoming->isEmpty())
                        <div class="sp-upcoming">
                            @foreach ($upcoming as $event)
                                <div class="sp-upcoming-item">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 text-gray-5 fs-13">
                                            <span class="sp-dot" style="background:{{ $colourFor($event) }}"></span>
                                            <span>{{ $when($event) }}</span>
                                        </div>
                                        <span class="fw-semibold mt-2 d-block text-truncate" title="{{ $event->title }}">
                                            @can('update', $event)
                                                <a href="{{ route('communication.events.edit', $event) }}" class="text-reset text-decoration-none">{{ $event->title }}</a>
                                            @else
                                                {{ $event->title }}
                                            @endcan
                                        </span>
                                    </div>
                                    @can('delete', $event)
                                        <div class="dropdown">
                                            <a aria-expanded="false" class="action-icon btn btn-icon btn-sm btn-outline-white" data-bs-toggle="dropdown" href="javascript:void(0);">
                                                <i class="ti ti-dots-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu p-2 border shadow">
                                                <li><a href="{{ route('communication.events.edit', $event) }}" class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2"><i class="ti ti-edit"></i>{{ __('communication.events.edit') }}</a></li>
                                                <li>
                                                    <form action="{{ route('communication.events.destroy', $event) }}" method="POST"
                                                        onsubmit="return confirm(@js(__('communication.events.confirm_delete')));">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2">
                                                            <i class="ti ti-trash"></i>{{ __('communication.events.delete') }}
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('communication.events.upcoming_none') }}</p>
                    @endunless
                </div>
            </div>
        </div>

        {{-- The month on a grid, drawn on the server: the arrows change the month in the address, the days come from the controller. --}}
        <div class="col-xxl-9 col-lg-8">
            <div class="card h-100 p-0">
                <div class="card-body p-4">
                    <div class="sp-cal-toolbar">
                        <div class="sp-cal-nav">
                            <a class="btn btn-sm btn-light" href="{{ route('communication.events.index', ['month' => $prevMonth]) }}" aria-label="{{ __('app.previous') }}"><i class="ti ti-chevron-left"></i></a>
                            <a class="btn btn-sm btn-light" href="{{ route('communication.events.index') }}">{{ __('communication.events.today') }}</a>
                            <a class="btn btn-sm btn-light" href="{{ route('communication.events.index', ['month' => $nextMonth]) }}" aria-label="{{ __('app.next') }}"><i class="ti ti-chevron-right"></i></a>
                        </div>
                        <h5 class="sp-cal-title">{{ $month->translatedFormat('F Y') }}</h5>
                    </div>

                    <div class="sp-cal-weekdays">
                        @foreach ($weekdays as $weekday)
                            <div>{{ $weekday }}</div>
                        @endforeach
                    </div>

                    <div class="sp-cal-grid">
                        @foreach ($weeks as $week)
                            @foreach ($week as $day)
                                @php($outside = ! $day->isSameMonth($month))
                                <div class="sp-cal-cell {{ $outside ? 'sp-cal-out' : '' }} {{ $day->isToday() ? 'sp-cal-today' : '' }}">
                                    <div class="sp-cal-head">
                                        <span class="sp-cal-day">{{ $day->translatedFormat('j') }}</span>
                                    </div>
                                    @foreach ($byDay->get($day->toDateString(), collect())->take(3) as $event)
                                        @php($edit = auth()->user()->can('update', $event))
                                        <{{ $edit ? 'a' : 'div' }} @if ($edit) href="{{ route('communication.events.edit', $event) }}" @endif class="sp-cal-event" style="color:{{ $colourFor($event) }}" title="{{ $event->title }}">
                                            <span class="sp-dot" style="background:{{ $colourFor($event) }}"></span>
                                            <span class="sp-cal-event-text">
                                                <span class="fw-semibold" @if ($edit) style="color:{{ $colourFor($event) }}" @endif>@if ($event->starts_at->format('H:i') !== '00:00'){{ $event->starts_at->format('H:i') }} @endif</span>{{ $event->title }}
                                            </span>
                                        </{{ $edit ? 'a' : 'div' }}>
                                    @endforeach
                                    @if ($byDay->get($day->toDateString(), collect())->count() > 3)
                                        <span class="fs-11 text-muted">+{{ $byDay->get($day->toDateString())->count() - 3 }}</span>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    @if ($byDay->isEmpty())
                        <p class="text-muted text-center mb-0 mt-3">{{ __('communication.events.empty') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@can('create', App\Models\Event::class)
    <x-form-modal id="event-modal" title="{{ __('communication.events.add') }}" action="{{ route('communication.events.store') }}" submit="{{ __('communication.events.add') }}">
        @include('communication.events._fields', ['event' => null, 'prefill' => $month->format('Y-m-d').'T08:00'])
    </x-form-modal>
@endcan
@endsection
