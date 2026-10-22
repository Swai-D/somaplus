@extends('layouts.app')

@section('title', __('menu.add_guardian'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('guardians.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('students.guardians.list.back') }}</a>
        <h3 class="page-title mt-2 mb-1">{{ __('menu.add_guardian') }}</h3>
        <p class="text-muted mb-0">{{ __('students.guardians.create.intro') }}</p>
    </div>

    <form method="POST" action="{{ route('guardians.store') }}">
        @csrf
        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('students.guardians.form.personal') }}</h5></div>
            <div class="card-body">
                @include('students.guardians._form')
            </div>
            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('students.guardians.form.save') }}</button>
                <a href="{{ route('guardians.index') }}" class="btn btn-outline-secondary">{{ __('students.guardians.form.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
