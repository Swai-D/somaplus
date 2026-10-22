<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">{{ __('students.fields.name') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name', $guardian->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" maxlength="150" required>
        @error('name')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="phone">{{ __('students.fields.phone') }}</label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $guardian->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror" maxlength="30" required>
        @error('phone')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="email">{{ __('students.fields.email') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email', $guardian->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" maxlength="150">
        @error('email')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        <div class="form-text">{{ __('students.guardians.form.only_what_is_needed') }}</div>
    </div>
</div>
