{{-- The fields of a notice. Variables: notice (Notice|null). --}}
<div class="row gy-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="notice-day">{{ __('communication.notices.day') }} <span class="text-danger">*</span></label>
        <input class="form-control" id="notice-day" name="day" type="date" value="{{ old('day', $notice?->day?->toDateString()) }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold" for="notice-title">{{ __('communication.notices.title') }} <span class="text-danger">*</span></label>
        <input class="form-control" id="notice-title" name="title" type="text" maxlength="200" value="{{ old('title', $notice?->title) }}" required autocomplete="off">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold" for="notice-description">{{ __('communication.notices.description') }}</label>
        <textarea class="form-control" id="notice-description" name="description" rows="4" maxlength="5000">{{ old('description', $notice?->description) }}</textarea>
    </div>
</div>
