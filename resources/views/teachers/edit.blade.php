@extends('layouts.app')

@section('title', __('teachers.list.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('teachers.show', $teacher) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $teacher->fullName() }}</a>
        <h3 class="page-title mt-2">{{ __('teachers.list.edit') }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('teachers.update', $teacher) }}" method="POST">
                @csrf
                @method('PUT')
                @include('teachers._fields', ['teacher' => $teacher, 'subjects' => $subjects, 'classes' => $classes])
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('teachers.form.save') }}</button>
                <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-secondary">{{ __('teachers.form.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
