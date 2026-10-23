<?php

namespace App\Http\Controllers\Communication;

use App\Domain\Communication\RemoveNotice;
use App\Domain\Communication\SaveNotice;
use App\Http\Controllers\Controller;
use App\Http\Requests\Communication\NoticeRequest;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The school's notice board: the notices on it, putting a new one up, changing
 * one and taking one down. Reading the board needs `communication.view` and
 * changing it needs `communication.manage` (the policy).
 */
class NoticeController extends Controller
{
    private const LIST_LIMIT = 200;

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Notice::class);

        $notices = Notice::query()->orderByDesc('day')->orderByDesc('id')->limit(self::LIST_LIMIT)->get();

        return view('communication.notices.index', ['notices' => $notices]);
    }

    public function store(NoticeRequest $request, SaveNotice $save): RedirectResponse
    {
        /** @var array{day: string, title: string, description?: string|null} $data */
        $data = $request->validated();

        $save->handle(null, $data);

        return redirect()->route('communication.notices.index')->with('status', __('communication.notices.saved'));
    }

    public function edit(Notice $notice): View
    {
        Gate::authorize('update', $notice);

        return view('communication.notices.edit', ['notice' => $notice]);
    }

    public function update(NoticeRequest $request, Notice $notice, SaveNotice $save): RedirectResponse
    {
        Gate::authorize('update', $notice);

        /** @var array{day: string, title: string, description?: string|null} $data */
        $data = $request->validated();

        $save->handle($notice, $data);

        return redirect()->route('communication.notices.index')->with('status', __('communication.notices.saved'));
    }

    public function destroy(Notice $notice, RemoveNotice $remove): RedirectResponse
    {
        Gate::authorize('delete', $notice);

        $remove->handle($notice);

        return redirect()->route('communication.notices.index')->with('status', __('communication.notices.deleted'));
    }
}
