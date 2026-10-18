{{-- A delete button with a confirmation. Variables: action. --}}
<form action="{{ $action }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('academics.confirm_delete')))">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('academics.delete') }}</button>
</form>
