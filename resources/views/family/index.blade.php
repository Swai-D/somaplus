@extends('layouts.app')

@section('title', __('family.title'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('family.title') }}</h3>
        <p class="text-muted mb-0">{{ __('family.intro') }}</p>
    </div>

    @forelse ($rows as $row)
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">{{ $row['student']->fullName() }}</h5>
                    <div class="text-muted fs-13">{{ $row['student']->admission_number }}</div>
                    <div class="mt-2">
                        @if ($row['owed']->isEmpty())
                            <span class="badge bg-success">{{ __('family.nothing_owed') }}</span>
                        @else
                            @foreach ($row['owed'] as $currency => $minor)
                                <span class="badge bg-warning text-dark">{{ __('family.owed') }}: {{ \App\Support\Money::of($minor, $currency)->format() }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
                <a href="{{ route('family.children.show', $row['student']->id) }}" class="btn btn-primary">{{ __('family.open') }}</a>
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-muted">{{ __('family.no_children') }}</div></div>
    @endforelse
@endsection
