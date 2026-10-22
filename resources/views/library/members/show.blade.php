@extends('layouts.app')

@section('title', $member->fullName())

@section('page')
    <div class="mb-3">
        <a href="{{ route('library.members.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.library_members') }}</a>
        <h3 class="page-title mt-2">{{ $member->fullName() }}</h3>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <h5 class="mb-0">{{ __('library.members.show.details') }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('library.members.card', $member) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-id me-1"></i>{{ __('library.members.show.print_card') }}</a>
                        @can('update', $member)
                            <a href="{{ route('library.members.edit', $member) }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-edit me-1"></i>{{ __('library.members.show.edit') }}</a>
                        @endcan
                        @can('delete', $member)
                            <form action="{{ route('library.members.destroy', $member) }}" method="POST" onsubmit="return confirm('{{ __('library.members.show.delete_confirm') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="ti ti-trash me-1"></i>{{ __('library.members.show.delete') }}</button>
                            </form>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('library.members.show.card') }}</dt>
                        <dd class="col-sm-8">{{ $member->card_number }}</dd>
                        <dt class="col-sm-4">{{ __('library.members.show.pupil') }}</dt>
                        <dd class="col-sm-8">{{ $member->fullName() }}</dd>
                        <dt class="col-sm-4">{{ __('library.members.show.class') }}</dt>
                        <dd class="col-sm-8">{{ ($placements[$member->student_id] ?? null)?->schoolClass?->name ?? __('library.members.list.no_class') }}</dd>
                        <dt class="col-sm-4">{{ __('library.members.show.phone') }}</dt>
                        <dd class="col-sm-8" title="{{ __('library.members.show.phone_hint') }}">{{ $member->student->guardians->first()?->phone ?? __('library.members.show.no_guardian') }}</dd>
                        <dt class="col-sm-4">{{ __('library.members.show.joined') }}</dt>
                        <dd class="col-sm-8">{{ $member->joined_on->isoFormat('D MMM YYYY') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('library.members.show.history') }}</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="datatable table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('library.issues.list.book') }}</th>
                            <th>{{ __('library.issues.list.number') }}</th>
                            <th>{{ __('library.issues.list.issued_on') }}</th>
                            <th>{{ __('library.issues.list.due_on') }}</th>
                            <th>{{ __('library.issues.list.returned_on') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->issues as $issue)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $issue->book->name }}</td>
                                <td class="text-muted">{{ $issue->book->book_number ?? '—' }}</td>
                                <td>{{ $issue->issued_on->isoFormat('D MMM YYYY') }}</td>
                                <td>
                                    {{ $issue->due_on?->isoFormat('D MMM YYYY') ?? '—' }}
                                    @if ($issue->isOverdue())
                                        <span class="badge bg-danger ms-1">{{ __('library.issues.list.overdue') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($issue->isReturned())
                                        <span class="text-muted">{{ $issue->returned_on->isoFormat('D MMM YYYY') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('library.members.show.out') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if (! $issue->isReturned())
                                        @can('update', $issue)
                                            <form action="{{ route('library.issues.return', $issue) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('library.issues.list.issue') }}</button>
                                            </form>
                                        @endcan
                                    @else
                                        <span class="badge bg-success">{{ __('library.members.show.returned') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('library.members.show.no_history') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
