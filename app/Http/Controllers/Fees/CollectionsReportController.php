<?php

namespace App\Http\Controllers\Fees;

use App\Domain\Fees\CollectionsReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\CollectionsReportRequest;
use Illuminate\View\View;

class CollectionsReportController extends Controller
{
    public function __invoke(CollectionsReportRequest $request, CollectionsReport $report): View
    {
        $from = $request->from();
        $to = $request->to();

        return view('fees.reports.collections', [
            'from' => $from,
            'to' => $to,
            'report' => $report->handle($from, $to),
        ]);
    }
}
