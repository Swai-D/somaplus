@extends('layouts.app')

@section('title', __('library.books.form.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('library.books.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.book_list') }}</a>
        <h3 class="page-title mt-2">{{ __('library.books.form.edit') }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('library.books.update', $book) }}" method="POST">
                @csrf
                @method('PUT')
                @include('library.books._fields', ['book' => $book, 'subjects' => $subjects])
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('library.books.form.save') }}</button>
                <a href="{{ route('library.books.index') }}" class="btn btn-outline-secondary">{{ __('library.books.form.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
