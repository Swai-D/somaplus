@extends('layouts.app')

@section('title', __('menu.notice_board'))

@section('page')
<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4>{{ __('menu.notice_board') }}</h4>
            <h6>{{ __('menu.sections.communication') }}</h6>
        </div>
    </div>
    <ul class="table-top-head">
        <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li>
        <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li>
    </ul>
    @can('create', App\Models\Notice::class)
        <div class="page-btn">
            <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#notice-modal" data-bs-toggle="modal" href="javascript:void(0);">
                <i class="ti ti-circle-plus me-1"></i>{{ __('communication.notices.add') }}
            </a>
        </div>
    @endcan
</div>

<p class="text-muted mt-2 mb-0">
    {{ __('communication.notices.intro') }}
</p>

<div class="mt-4">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2"></div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="datatable table">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('communication.notices.day') }}</th>
                        <th scope="col">{{ __('communication.notices.title') }}</th>
                        <th scope="col">{{ __('communication.notices.description') }}</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($notices as $notice)
                        @php($manageable = auth()->user()->can('update', $notice))
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="text-nowrap">
                                {{-- The machine day stays in the element for a comparing eye; the printed day is the human one. --}}
                                <time datetime="{{ $notice->day->toDateString() }}">{{ $notice->day->translatedFormat('d M Y') }}</time>
                            </td>
                            <td class="fw-medium">{{ $notice->title }}</td>
                            <td title="{{ $notice->description }}">
                                {{ $notice->description ? \Illuminate\Support\Str::limit($notice->description, 120) : '—' }}
                            </td>
                            <td class="action-table-data text-end">
                                <div class="edit-delete-action d-flex align-items-center justify-content-end gap-2">
                                    @if ($manageable)
                                        <a href="{{ route('communication.notices.edit', $notice) }}" class="p-2 d-flex align-items-center border rounded" title="{{ __('communication.notices.edit') }}">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                    @endif
                                    @can('delete', $notice)
                                        <form action="{{ route('communication.notices.destroy', $notice) }}" method="POST"
                                            onsubmit="return confirm(@js(__('communication.notices.confirm_delete')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 d-flex align-items-center border rounded text-danger" title="{{ __('communication.notices.delete') }}">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">{{ __('communication.notices.empty') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@can('create', App\Models\Notice::class)
    <x-form-modal id="notice-modal" title="{{ __('communication.notices.add') }}" action="{{ route('communication.notices.store') }}" submit="{{ __('communication.notices.add') }}">
        @include('communication.notices._fields', ['notice' => null])
    </x-form-modal>
@endcan
@endsection
