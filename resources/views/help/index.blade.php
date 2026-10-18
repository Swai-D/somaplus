@extends('layouts.app')

@section('title', __('help.title'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('help.title') }}</h3>
        <p class="text-muted mb-0">{{ __('help.intro') }}</p>
    </div>

    <div class="row">
        @foreach ($articles as $slug => $article)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('help.show', $slug) }}" class="card text-decoration-none">
                    <div class="card-body">
                        <h5 class="mb-2">{{ $article['title'] }}</h5>
                        <p class="text-muted mb-0">{{ $article['summary'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
