<?php

namespace App\Http\Controllers\Fees;

use App\Domain\Settings\RecordAudit;
use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function show(Receipt $receipt, RecordAudit $audit): View
    {
        Gate::authorize('view', $receipt);

        $audit->handle('receipt.viewed', $receipt);

        return view('fees.receipts.show', [
            'receipt' => $receipt->load(['payment.invoice.student', 'payment.invoice.term.academicYear', 'payment.reversal']),
        ]);
    }
}
