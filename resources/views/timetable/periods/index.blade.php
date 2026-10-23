@extends('layouts.app')

@section('title', __('menu.timetable_periods'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.timetable_periods') }}</h3>
            <p class="text-muted mb-0">{{ __('timetable.periods.intro') }}</p>
        </div>
        @can('create', App\Models\TimetablePeriod::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#period-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('timetable.periods.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            @if ($periods->isEmpty())
                <p class="text-muted mb-0">{{ __('timetable.periods.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('timetable.periods.name') }}</th>
                                <th>{{ __('timetable.periods.time') }}</th>
                                <th>{{ __('timetable.periods.type') }}</th>
                                <th>{{ __('timetable.periods.lessons') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periods as $period)
                                <tr>
                                    <td class="fw-medium">{{ $period->name }}</td>
                                    <td>{{ $period->range() }}</td>
                                    <td>{{ $period->is_break ? __('timetable.periods.break') : __('timetable.periods.lesson') }}</td>
                                    <td>{{ $period->entries_count }}</td>
                                    <td class="text-end">
                                        @can('update', $period)
                                            <a href="{{ route('timetable.periods.edit', $period) }}" class="btn btn-sm btn-outline-secondary">{{ __('timetable.edit') }}</a>
                                        @endcan
                                        @can('delete', $period)
                                            <form action="{{ route('timetable.periods.destroy', $period) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('timetable.periods.confirm_delete')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('timetable.delete') }}</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @can('create', App\Models\TimetablePeriod::class)
        <x-form-modal id="period-modal" title="{{ __('timetable.periods.add') }}" action="{{ route('timetable.periods.store') }}">
            @include('timetable.periods._fields', ['period' => null])
        </x-form-modal>
    @endcan
@endsection
