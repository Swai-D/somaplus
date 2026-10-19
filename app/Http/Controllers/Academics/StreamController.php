<?php

namespace App\Http\Controllers\Academics;

use App\Domain\Academics\DeleteAcademicRecord;
use App\Domain\Academics\SaveStream;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StreamRequest;
use App\Models\SchoolClass;
use App\Models\Stream;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StreamController extends Controller
{
    public function edit(Stream $stream): View
    {
        Gate::authorize('update', $stream);

        return view('academics.streams.edit', ['stream' => $stream, 'class' => $stream->schoolClass]);
    }

    public function store(StreamRequest $request, SchoolClass $schoolClass, SaveStream $save): RedirectResponse
    {
        /** @var array{name: string} $data */
        $data = $request->validated();
        $save->handle($schoolClass, $data);

        return redirect()->route('academics.classes.show', $schoolClass)->with('status', __('academics.saved'));
    }

    public function update(StreamRequest $request, Stream $stream, SaveStream $save): RedirectResponse
    {
        /** @var array{name: string} $data */
        $data = $request->validated();
        $class = $stream->schoolClass;
        $save->handle($class, $data, $stream);

        return redirect()->route('academics.classes.show', $class)->with('status', __('academics.saved'));
    }

    public function destroy(Stream $stream, DeleteAcademicRecord $delete): RedirectResponse
    {
        Gate::authorize('delete', $stream);

        $class = $stream->schoolClass;
        $delete->handle($stream);

        return redirect()->route('academics.classes.show', $class)->with('status', __('academics.deleted'));
    }
}
