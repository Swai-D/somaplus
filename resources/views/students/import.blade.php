@extends('layouts.app')

@section('title', __('students.import.title'))

@section('page')
    @php($result = session('import_result'))
    <div class="mb-3">
        <a href="{{ route('students.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('students.import.back') }}</a>
        <h3 class="page-title mt-2 mb-1">{{ __('students.import.title') }}</h3>
        <p class="text-muted mb-0">{{ __('students.import.hint') }}</p>
    </div>

    @if ($result)
        <div class="alert alert-success" role="status">
            <strong>{{ __('students.import.done') }}</strong>
            {{ __('students.import.summary', ['created' => $result['created'], 'placed' => $result['placed'], 'guardians' => $result['guardians']]) }}
        </div>
    @endif

    @if (session('import_problems'))
        <div class="alert alert-danger" role="alert">
            <strong>{{ __('students.import.refused', ['count' => session('import_total')]) }}</strong>
            <ul class="mb-0 mt-2">
                @foreach (session('import_problems') as $problem)<li>{{ $problem }}</li>@endforeach
            </ul>
            @if (session('import_total') > count(session('import_problems')))
                <div class="mt-2">{{ __('students.import.and_more', ['count' => session('import_total') - count(session('import_problems'))]) }}</div>
            @endif
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if ($year === null)
        <div class="alert alert-warning">{{ __('students.import.no_year') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('students.import.step1') }}</h5></div>
        <div class="card-body">
            <p>{{ __('students.import.step1_text') }}</p>
            <a href="{{ route('students.import.template') }}" class="btn btn-outline-primary"><i class="ti ti-download me-1"></i>{{ __('students.import.template') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('students.import.step2') }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('students.import.store') }}" enctype="multipart/form-data" class="d-flex flex-wrap gap-2 align-items-end">
                @csrf
                <div>
                    <label class="form-label" for="file">{{ __('students.import.file') }}</label>
                    <input type="file" id="file" name="file" accept=".csv,text/csv,text/plain" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary"><i class="ti ti-upload me-1"></i>{{ __('students.import.upload') }}</button>
            </form>
            <small class="text-muted d-block mt-2">{{ __('students.import.rules', ['max' => $max]) }}</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('students.import.columns') }}</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                        @foreach ($columns as $column)
                            <tr>
                                <td class="fw-semibold"><code>{{ $column }}</code>@if (in_array($column, $required, true))<span class="text-danger ms-1">*</span>@endif</td>
                                <td>{{ __('students.import.col.'.($column === 'admission_number' && $auto ? 'admission_number_auto' : $column)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('students.import.classes_in_school') }}</h5></div>
        <div class="card-body">
            @if ($classes->isEmpty())
                <p class="text-muted mb-0">{{ __('students.import.no_classes') }}</p>
            @else
                @foreach ($classes as $class)<span class="badge bg-light text-dark me-1">{{ $class->name }}</span>@endforeach
            @endif
        </div>
    </div>
@endsection
