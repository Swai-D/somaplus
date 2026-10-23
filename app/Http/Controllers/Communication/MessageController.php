<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\SmsMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * All the messages the school has sent through its SMS gateway: what was
 * queued, what went out, what failed and why. The phone numbers and the texts
 * are not kept anywhere, so this is only the record of how each message went.
 */
class MessageController extends Controller
{
    private const LIST_LIMIT = 200;

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', SmsMessage::class);

        $status = in_array((string) $request->query('status'), [
            SmsMessage::QUEUED, SmsMessage::SENT, SmsMessage::FAILED, SmsMessage::CANCELLED,
        ], true) ? (string) $request->query('status') : '';

        $kind = in_array((string) $request->query('kind'), [SmsMessage::KIND_REMINDER], true)
            ? (string) $request->query('kind')
            : '';

        $messages = SmsMessage::query()->with(['invoice.student', 'guardian'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($kind !== '', fn ($query) => $query->where('kind', $kind))
            ->latest('id')
            ->limit(self::LIST_LIMIT)
            ->get();

        return view('communication.messages.index', [
            'messages' => $messages,
            'status' => $status,
            'kinds' => [
                SmsMessage::KIND_REMINDER => __('communication.kinds.'.SmsMessage::KIND_REMINDER),
            ],
            'kind' => $kind,
        ]);
    }
}
