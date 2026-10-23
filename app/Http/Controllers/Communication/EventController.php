<?php

namespace App\Http\Controllers\Communication;

use App\Domain\Communication\RemoveEvent;
use App\Domain\Communication\SaveEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Communication\EventRequest;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The school's calendar of events: the month on a grid, what is coming next, and
 * putting an event on, changing one and taking one off. Reading needs
 * `communication.view` and changing needs `communication.manage` (the policy).
 */
class EventController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Event::class);

        $month = $this->month($request);
        $today = CarbonImmutable::today();

        // The events on the grid: the grid reaches into the month before and
        // after, so the edges of that grid are what the query covers.
        $grid = Event::query()
            ->whereBetween('starts_at', [$month->startOfMonth()->startOfWeek(CarbonImmutable::SUNDAY), $month->endOfMonth()->endOfWeek(CarbonImmutable::SATURDAY)->endOfDay()])
            ->orderBy('starts_at')->get()
            ->groupBy(fn (Event $event): string => $event->starts_at->format('Y-m-d'));

        $upcoming = Event::query()
            ->whereRaw('coalesce(ends_at, starts_at) >= ?', [(string) $today->startOfDay()])
            ->orderByRaw('coalesce(ends_at, starts_at), starts_at')
            ->orderBy('title')
            ->limit(10)
            ->get();

        return view('communication.events.index', [
            'month' => $month,
            'weeks' => $this->weeks($month),
            'byDay' => $grid,
            'upcoming' => $upcoming,
            'prevMonth' => $month->subMonth()->format('Y-m'),
            'nextMonth' => $month->addMonth()->format('Y-m'),
        ]);
    }

    public function store(EventRequest $request, SaveEvent $save): RedirectResponse
    {
        /** @var array{starts_at: string, ends_at?: string|null, title: string, description?: string|null} $data */
        $data = $request->validated();

        $event = $save->handle(null, $data);

        return redirect()
            ->route('communication.events.index', ['month' => $event->starts_at->format('Y-m')])
            ->with('status', __('communication.events.saved'));
    }

    public function edit(Event $event): View
    {
        Gate::authorize('update', $event);

        return view('communication.events.edit', ['event' => $event]);
    }

    public function update(EventRequest $request, Event $event, SaveEvent $save): RedirectResponse
    {
        Gate::authorize('update', $event);

        /** @var array{starts_at: string, ends_at?: string|null, title: string, description?: string|null} $data */
        $data = $request->validated();

        $save->handle($event, $data);

        return redirect()
            ->route('communication.events.index', ['month' => $event->starts_at->format('Y-m')])
            ->with('status', __('communication.events.saved'));
    }

    public function destroy(Event $event, RemoveEvent $remove): RedirectResponse
    {
        Gate::authorize('delete', $event);

        $remove->handle($event);

        return redirect()
            ->route('communication.events.index', ['month' => $event->starts_at->format('Y-m')])
            ->with('status', __('communication.events.deleted'));
    }

    /** The month the calendar shows: the one asked for in the query string, or this month. */
    private function month(Request $request): CarbonImmutable
    {
        $asked = (string) $request->query('month', '');

        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $asked)) {
            return CarbonImmutable::today()->startOfMonth();
        }

        return CarbonImmutable::parse($asked.'-01')->startOfMonth();
    }

    /**
     * The days of the calendar, in rows of a week, the weeks complete so the
     * first and the last row may reach into the month before or after.
     *
     * @return list<list<CarbonImmutable>>
     */
    private function weeks(CarbonImmutable $month): array
    {
        $days = iterator_to_array(CarbonPeriod::create(
            $month->startOfMonth()->startOfWeek(CarbonImmutable::SUNDAY),
            $month->endOfMonth()->endOfWeek(CarbonImmutable::SATURDAY),
        ));

        /** @var list<list<CarbonImmutable>> */
        return array_chunk($days, 7);
    }
}
