<?php

namespace App\Http\Controllers\Timetable;

use App\Domain\Timetable\DeleteTimetablePeriod;
use App\Domain\Timetable\SaveTimetablePeriod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Timetable\PeriodRequest;
use App\Models\TimetablePeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PeriodController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', TimetablePeriod::class);

        return view('timetable.periods.index', [
            'periods' => TimetablePeriod::query()->withCount('entries')->orderBy('starts_at')->get(),
        ]);
    }

    public function edit(TimetablePeriod $period): View
    {
        Gate::authorize('update', $period);

        return view('timetable.periods.edit', ['period' => $period]);
    }

    public function store(PeriodRequest $request, SaveTimetablePeriod $save): RedirectResponse
    {
        /** @var array{name: string, starts_at: string, ends_at: string, is_break?: bool} $data */
        $data = $request->validated();
        $save->handle($data);

        return redirect()->route('timetable.periods.index')->with('status', __('timetable.periods.saved'));
    }

    public function update(PeriodRequest $request, TimetablePeriod $period, SaveTimetablePeriod $save): RedirectResponse
    {
        /** @var array{name: string, starts_at: string, ends_at: string, is_break?: bool} $data */
        $data = $request->validated();
        $save->handle($data, $period);

        return redirect()->route('timetable.periods.index')->with('status', __('timetable.periods.saved'));
    }

    public function destroy(TimetablePeriod $period, DeleteTimetablePeriod $delete): RedirectResponse
    {
        Gate::authorize('delete', $period);

        $delete->handle($period);

        return redirect()->route('timetable.periods.index')->with('status', __('timetable.periods.deleted'));
    }
}
