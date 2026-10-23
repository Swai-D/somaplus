{{--
    One week of lessons, a column per day, a card per lesson (the template's timetable markup).
    Variables: week (days, week), show ("class" shows the teacher, "teacher" shows the class), canManage.
--}}
@php($tones = ['warning', 'info', 'success', 'danger', 'primary'])
<div class="overflow-x-auto d-flex pb-2">
    <div class="d-flex gap-3 flex-shrink-0 flex-grow-1">
        @foreach ($week['days'] as $day)
            <div class="flex-grow-1" style="min-width: 220px">
                <h6 class="mb-2">{{ __('timetable.days.'.$day) }}</h6>
                <div class="d-flex flex-column gap-3">
                    @forelse ($week['week'][$day] ?? [] as $entry)
                        @php($tone = $tones[$entry->subject_id % count($tones)])
                        <div class="attendance-card border rounded overflow-hidden">
                            <h6 class="bg-{{ $tone }}-transparent text-{{ $tone }} fw-semibold py-2 px-3 text-center mb-0 card-title">{{ $entry->subject->name }}</h6>
                            <div class="px-2 py-3 d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-flex text-gray-5"><i class="ti ti-clock"></i></span>
                                    <span class="text-dark">{{ $entry->period->range() }}</span>
                                </div>
                                @if ($show === 'teacher')
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-flex text-gray-5"><i class="ti ti-school"></i></span>
                                        <span class="text-dark">{{ $entry->groupLabel() }}</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-flex text-gray-5"><i class="ti ti-user-check"></i></span>
                                        <span class="text-dark">{{ $entry->teacher?->fullName() ?: __('timetable.no_teacher') }}</span>
                                    </div>
                                @endif
                                @if ($entry->room)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-flex text-gray-5"><i class="ti ti-building"></i></span>
                                        <span class="text-dark">{{ __('timetable.room') }}: {{ $entry->room }}</span>
                                    </div>
                                @endif
                                @if ($canManage ?? false)
                                    <div class="d-flex gap-2 pt-1 d-print-none">
                                        <a href="{{ route('timetable.entries.edit', $entry) }}" class="btn btn-sm btn-outline-secondary">{{ __('timetable.edit') }}</a>
                                        <form action="{{ route('timetable.entries.destroy', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('timetable.confirm_delete')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('timetable.delete') }}</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-muted fs-13">{{ __('timetable.free_day') }}</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
