{{-- The fields of a member of the library, used by the pop-up of the list and by the page
     that changes a member. Variables: member (LibraryMember or null), candidates (pupils). --}}
@php($editing = isset($member))

<div class="row">
    <div class="col-md-6 mb-3">
        @php($chosenStudent = old('student_id', $member?->student_id ?? ''))
        <label class="form-label" for="student_id">{{ __('library.members.form.student') }}<span class="text-danger"> *</span></label>
        <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
            <option value="">{{ __('library.members.form.choose_pupil') }}</option>
            @foreach ($candidates as $candidate)
                <option value="{{ $candidate->id }}" @selected($chosenStudent == $candidate->id)>{{ $candidate->fullName() }}</option>
            @endforeach
        </select>
        <div class="form-text">{{ __('library.members.form.student_hint') }}</div>
        @error('student_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        @php($cardValue = $member?->card_number ?? '')
        @include('academics._field', ['name' => 'card_number', 'label' => __('library.members.form.card'), 'value' => $cardValue, 'hint' => __('library.members.form.card_hint')])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'joined_on', 'label' => __('library.members.form.joined_on'), 'type' => 'date', 'value' => $member?->joined_on?->toDateString() ?? now()->toDateString()])
    </div>
</div>
