@extends('layouts.app')

@section('title', __('menu.guardians'))

@section('page')
    @php
        // The pop-up opens on its own when the form was refused, when the page was opened
        // to change one guardian (the button on the page of a guardian) or to add one
        // (the entry of the menu).
        $editing = null;
        if (! $errors->any() && request()->filled('edit')) {
            $editing = \App\Models\Guardian::query()->whereKey((int) request('edit'))->first();
        }
        $adding = request()->filled('add') && $editing === null;
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.guardians') }}</h3>
            <p class="text-muted mb-0">{{ __('students.guardians.list.intro') }}</p>
        </div>
        @can('create', App\Models\Guardian::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#guardian-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('students.guardians.list.add') }}
            </button>
        @endcan
    </div>

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
                <table class="datatable table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('students.fields.name') }}</th>
                            <th>{{ __('students.guardians.list.children') }}</th>
                            <th>{{ __('students.fields.phone') }}</th>
                            <th>{{ __('students.fields.email') }}</th>
                            <th>{{ __('students.guardians.list.joined') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guardians as $guardian)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-medium">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-md bg-light text-dark fw-semibold">{{ mb_strtoupper(mb_substr($guardian->name, 0, 1)) }}</span>
                                        <a href="{{ route('guardians.show', $guardian) }}">{{ $guardian->name }}</a>
                                    </div>
                                </td>
                                <td>
                                    @if ($guardian->students->isEmpty())
                                        <span class="text-muted">{{ __('students.guardians.list.no_children') }}</span>
                                    @else
                                        @foreach ($guardian->students as $child)
                                            @php($placement = $placements[$child->id] ?? null)
                                            <div>
                                                <a href="{{ route('students.show', $child) }}">{{ $child->fullName() }}</a>
                                                @if ($placement)
                                                    <span class="text-muted fs-12">{{ $placement->schoolClass->name }}@if ($placement->stream) – {{ $placement->stream->name }}@endif</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $guardian->phone }}</td>
                                <td>{{ $guardian->email }}</td>
                                <td class="text-muted">{{ $guardian->created_at->toFormattedDateString() }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('guardians.show', $guardian) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('academics.open') }}"><i class="ti ti-eye"></i></a>
                                        @can('update', $guardian)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="{{ __('students.guardians.list.edit') }}"
                                                data-bs-target="#guardian-modal" data-bs-toggle="modal"
                                                data-guardian-action="{{ route('guardians.update', $guardian) }}"
                                                data-guardian-title="{{ __('students.guardians.list.edit') }}"
                                                data-guardian-submit="{{ __('students.guardians.form.save') }}"
                                                data-guardian-name="{{ $guardian->name }}"
                                                data-guardian-phone="{{ $guardian->phone }}"
                                                data-guardian-email="{{ $guardian->email }}">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('students.guardians.list.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('create', App\Models\Guardian::class)
        <div aria-hidden="true" class="modal fade" id="guardian-modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="page-title"><h4 data-guardian-heading>{{ __('students.guardians.list.add') }}</h4></div>
                        <button aria-label="{{ __('students.guardians.form.close') }}" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('guardians.store') }}" method="POST"
                        data-guardian-form
                        data-guardian-open="{{ ($errors->any() || $editing !== null || $adding) ? '1' : '0' }}"
                        data-guardian-old-name="{{ old('name') }}"
                        data-guardian-old-phone="{{ old('phone') }}"
                        data-guardian-old-email="{{ old('email') }}"
                        data-guardian-add-action="{{ route('guardians.store') }}"
                        data-guardian-add-title="{{ __('students.guardians.list.add') }}"
                        data-guardian-add-submit="{{ __('students.guardians.list.add') }}"
                        data-guardian-edit-action="{{ $editing ? route('guardians.update', $editing) : '' }}"
                        data-guardian-edit-title="{{ __('students.guardians.list.edit') }}"
                        data-guardian-edit-submit="{{ __('students.guardians.form.save') }}"
                        data-guardian-edit-name="{{ $editing?->name }}"
                        data-guardian-edit-phone="{{ $editing?->phone }}"
                        data-guardian-edit-email="{{ $editing?->email }}">
                        @csrf
                        <input name="_method" type="hidden" value="">
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                            @endif
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="guardian-name">{{ __('students.fields.name') }}<span class="text-danger ms-1">*</span></label>
                                    <input type="text" id="guardian-name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" maxlength="150" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="guardian-phone">{{ __('students.fields.phone') }}<span class="text-danger ms-1">*</span></label>
                                    <input type="tel" id="guardian-phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" maxlength="30" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="guardian-email">{{ __('students.fields.email') }}</label>
                                    <input type="email" id="guardian-email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" maxlength="150">
                                    <div class="form-text">{{ __('students.guardians.form.only_what_is_needed') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">{{ __('students.guardians.form.cancel') }}</button>
                            <button class="btn btn-primary" data-guardian-submit type="submit">{{ __('students.guardians.list.add') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @push('page-scripts')
        <script>
            // The add and edit form of a guardian is a pop-up, as in the template: the page is
            // not loaded again, the form of the pop-up is filled with the row that opened it.
            (function () {
                var modal = document.getElementById('guardian-modal');
                if (!modal) { return; }

                var form = modal.querySelector('[data-guardian-form]');
                var method = form.querySelector('input[name="_method"]');
                var heading = modal.querySelector('[data-guardian-heading]');
                var submit = modal.querySelector('[data-guardian-submit]');
                var name = modal.querySelector('#guardian-name');
                var phone = modal.querySelector('#guardian-phone');
                var email = modal.querySelector('#guardian-email');

                function fill(action, title, label, guardianName, guardianPhone, guardianEmail) {
                    form.action = action;
                    method.value = action === form.getAttribute('data-guardian-add-action') ? '' : 'PUT';
                    heading.textContent = title;
                    submit.textContent = label;
                    name.value = guardianName || '';
                    phone.value = guardianPhone || '';
                    email.value = guardianEmail || '';
                }

                modal.addEventListener('show.bs.modal', function (event) {
                    var trigger = event.relatedTarget;
                    var action = trigger && trigger.getAttribute('data-guardian-action');

                    if (action) {
                        fill(
                            action,
                            trigger.getAttribute('data-guardian-title'),
                            trigger.getAttribute('data-guardian-submit'),
                            trigger.getAttribute('data-guardian-name'),
                            trigger.getAttribute('data-guardian-phone'),
                            trigger.getAttribute('data-guardian-email')
                        );
                    } else {
                        fill(
                            form.getAttribute('data-guardian-add-action'),
                            form.getAttribute('data-guardian-add-title'),
                            form.getAttribute('data-guardian-add-submit'),
                            form.getAttribute('data-guardian-old-name'),
                            form.getAttribute('data-guardian-old-phone'),
                            form.getAttribute('data-guardian-old-email')
                        );
                    }
                });

                // The page was opened to change a guardian, to add one, or the form was refused:
                // the pop-up opens on its own, filled with what is known.
                if (form.getAttribute('data-guardian-open') === '1' && window.bootstrap) {
                    var editAction = form.getAttribute('data-guardian-edit-action');
                    if (editAction) {
                        fill(
                            editAction,
                            form.getAttribute('data-guardian-edit-title'),
                            form.getAttribute('data-guardian-edit-submit'),
                            form.getAttribute('data-guardian-edit-name'),
                            form.getAttribute('data-guardian-edit-phone'),
                            form.getAttribute('data-guardian-edit-email')
                        );
                    }

                    new window.bootstrap.Modal(modal).show();
                }
            })();
        </script>
    @endpush
@endsection
