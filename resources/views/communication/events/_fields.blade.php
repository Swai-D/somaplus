{{-- The fields of an event. Variables: event (Event|null), prefill (string). --}}
<div class="row gy-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="event-starts-at">{{ __('communication.events.starts_at') }} <span class="text-danger">*</span></label>
        <input class="form-control" id="event-starts-at" name="starts_at" type="datetime-local"
            value="{{ old('starts_at', $event?->starts_at?->format('Y-m-d\TH:i') ?? $prefill ?? null) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="event-ends-at">{{ __('communication.events.ends_at') }}</label>
        <input class="form-control" id="event-ends-at" name="ends_at" type="datetime-local"
            value="{{ old('ends_at', $event?->ends_at?->format('Y-m-d\TH:i')) }}">
        <div class="form-text">{{ __('communication.events.ends_hint') }}</div>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold" for="event-title">{{ __('communication.events.title') }} <span class="text-danger">*</span></label>
        <input class="form-control" id="event-title" name="title" type="text" maxlength="200" value="{{ old('title', $event?->title) }}" required autocomplete="off">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold" for="event-description">{{ __('communication.events.description') }}</label>
        <textarea class="form-control" id="event-description" name="description" rows="4" maxlength="5000">{{ old('description', $event?->description) }}</textarea>
    </div>
</div>
