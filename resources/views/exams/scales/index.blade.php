@extends('layouts.app')

@section('title', __('menu.grading_scales'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.grading_scales') }}</h3>
            <p class="text-muted mb-0">{{ __('exams.scales.intro') }}</p>
        </div>
        @can('create', App\Models\GradingScale::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#scale-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('exams.scales.add') }}
            </button>
        @endcan
    </div>

    @forelse ($scales as $scale)
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1">{{ $scale->name }}</h5>
                        <span class="text-muted fs-13">{{ trans_choice('exams.scales.used_by', $scale->exams_count, ['count' => $scale->exams_count]) }}</span>
                    </div>
                    <div>
                        @can('update', $scale)
                            <a href="{{ route('grading-scales.edit', $scale) }}" class="btn btn-sm btn-outline-secondary">{{ __('exams.edit') }}</a>
                        @endcan
                        @can('create', App\Models\GradingScale::class)
                            <form action="{{ route('grading-scales.copy', $scale) }}" method="POST" class="d-inline">@csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('exams.scales.copy') }}</button>
                            </form>
                        @endcan
                        @can('delete', $scale)
                            @if ($scale->exams_count === 0)
                                @include('academics._delete', ['action' => route('grading-scales.destroy', $scale)])
                            @endif
                        @endcan
                    </div>
                </div>
                @include('exams.scales._bands', ['bands' => $scale->bands])
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-muted">{{ __('exams.scales.empty') }}</div></div>
    @endforelse

    @can('create', App\Models\GradingScale::class)
        <x-form-modal id="scale-modal" title="{{ __('exams.scales.add') }}" action="{{ route('grading-scales.store') }}" size="modal-xl">
            @include('exams.scales._fields', ['scale' => null])
        </x-form-modal>
    @endcan
@endsection
