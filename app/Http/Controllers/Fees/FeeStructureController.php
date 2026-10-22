<?php

namespace App\Http\Controllers\Fees;

use App\Domain\Fees\DeleteFeeStructure;
use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\SaveFeeStructure;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\FeeStructureRequest;
use App\Http\Requests\Fees\GenerateInvoicesRequest;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FeeStructureController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', FeeStructure::class);

        return view('fees.structures.index', [
            'structures' => FeeStructure::query()->with(['term.academicYear', 'schoolClass', 'items'])->withCount('invoices')->orderByDesc('term_id')->orderBy('school_class_id')->orderBy('name')->get(),
        ] + $this->choices());
    }

    /**
     * The form of a new fee structure is a pop-up of the list, as in the template, so this
     * address only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', FeeStructure::class);

        return redirect()->route('fees.structures.index', ['add' => 'structure-modal']);
    }

    public function store(FeeStructureRequest $request, SaveFeeStructure $save): RedirectResponse
    {
        /** @var array{name: string, term_id: int|string, school_class_id: int|string, items: array<int, array<string, mixed>>} $data */
        $data = $request->validated();
        $structure = $save->handle($data);

        return redirect()->route('fees.structures.show', $structure)->with('status', __('fees.saved'));
    }

    public function show(FeeStructure $structure): View
    {
        Gate::authorize('view', $structure);

        return view('fees.structures.show', [
            'structure' => $structure->load(['term.academicYear', 'schoolClass', 'items']),
            'invoiceCount' => $structure->invoices()->count(),
            'streams' => Stream::query()->where('school_class_id', $structure->school_class_id)->orderBy('name')->get(),
        ]);
    }

    public function edit(FeeStructure $structure): View
    {
        Gate::authorize('update', $structure);

        return view('fees.structures.edit', ['structure' => $structure->load('items'), 'used' => $structure->isUsed()] + $this->choices());
    }

    public function update(FeeStructureRequest $request, FeeStructure $structure, SaveFeeStructure $save): RedirectResponse
    {
        /** @var array{name: string, term_id: int|string, school_class_id: int|string, items: array<int, array<string, mixed>>} $data */
        $data = $request->validated();
        $save->handle($data, $structure);

        return redirect()->route('fees.structures.show', $structure)->with('status', __('fees.saved'));
    }

    public function destroy(FeeStructure $structure, DeleteFeeStructure $delete): RedirectResponse
    {
        Gate::authorize('delete', $structure);

        $delete->handle($structure);

        return redirect()->route('fees.structures.index')->with('status', __('fees.deleted'));
    }

    public function generate(GenerateInvoicesRequest $request, FeeStructure $structure, GenerateInvoices $generate): RedirectResponse
    {
        $stream = $request->filled('stream_id') ? Stream::query()->findOrFail($request->integer('stream_id')) : null;
        $result = $generate->handle($structure, $stream);

        return redirect()->route('fees.structures.show', $structure)
            ->with('status', trans_choice('fees.structures.generated', $result['created'], $result));
    }

    /**
     * @return array<string, mixed>
     */
    private function choices(): array
    {
        return [
            'terms' => Term::query()->with('academicYear')->orderByDesc('starts_on')->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }
}
