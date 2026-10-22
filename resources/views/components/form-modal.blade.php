@props([
    'id',
    'title',
    'action',
    'method' => 'POST',
    'submit' => null,
    'size' => '',
])

{{--
    A form in a pop-up, as in the template's list pages. The form carries a hidden field
    with the id of the pop-up, so that when the form is refused the page knows which
    pop-up to open again.
--}}
<div aria-hidden="true" class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered {{ $size }}">
        <div class="modal-content">
            <div class="modal-header">
                <div class="page-title"><h4>{{ $title }}</h4></div>
                <button aria-label="{{ __('app.close') }}" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $action }}" method="POST">
                @csrf
                @if ($method !== 'POST')
                    <input name="_method" type="hidden" value="{{ $method }}">
                @endif
                <input name="form" type="hidden" value="{{ $id }}">
                <div class="modal-body">
                    {{ $slot }}
                </div>
                <div class="modal-footer">
                    <button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">{{ __('app.cancel') }}</button>
                    <button class="btn btn-primary" type="submit">{{ $submit ?? __('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
