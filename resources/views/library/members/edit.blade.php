@extends('layouts.app')

@section('title', __('library.members.form.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('library.members.show', $member) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $member->fullName() }}</a>
        <h3 class="page-title mt-2">{{ __('library.members.form.edit') }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('library.members.update', $member) }}" method="POST">
                @csrf
                @method('PUT')
                @include('library.members._fields', ['member' => $member, 'candidates' => $candidates])
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('library.members.form.save') }}</button>
                <a href="{{ route('library.members.show', $member) }}" class="btn btn-outline-secondary">{{ __('library.members.form.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
