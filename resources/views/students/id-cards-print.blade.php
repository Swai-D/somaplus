<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __('idcard.title') }}</title>
    <style>
        @page { size: A4; margin: 10mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef0f3; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #111827; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bar { position: sticky; top: 0; z-index: 2; display: flex; gap: 12px; align-items: center; justify-content: space-between; padding: 12px 20px; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .bar p { margin: 0; color: #6b7280; font-size: 14px; }
        .bar button { background: #fe9f43; color: #fff; border: 0; border-radius: 8px; padding: 10px 18px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .sheet { display: grid; grid-template-columns: repeat(2, 85.6mm); gap: 5mm; justify-content: center; padding: 16px; }
        .card { position: relative; width: 85.6mm; height: 54mm; background: #fff; border: .25mm solid #cbd5e1; border-radius: 3mm; overflow: hidden; display: flex; flex-direction: column; break-inside: avoid; page-break-inside: avoid; }
        .head { background: #1f2937; color: #fff; padding: 2.2mm 4mm; display: flex; justify-content: space-between; align-items: center; gap: 3mm; border-bottom: 1.1mm solid #fe9f43; }
        .head .school { font-size: 3.1mm; font-weight: 700; line-height: 1.15; text-transform: uppercase; letter-spacing: .02em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .head .tag { font-size: 2.2mm; opacity: .8; white-space: nowrap; text-transform: uppercase; letter-spacing: .08em; }
        .body { flex: 1; display: flex; gap: 3.5mm; padding: 3mm 4mm 0; min-height: 0; }
        .avatar { flex: none; width: 17mm; height: 17mm; border-radius: 50%; background: #fff3e6; color: #c2570c; border: .5mm solid #fe9f43; display: flex; align-items: center; justify-content: center; font-size: 6mm; font-weight: 700; margin-top: .5mm; }
        .info { min-width: 0; flex: 1; }
        .name { font-size: 4.2mm; font-weight: 700; line-height: 1.12; margin: 0 0 1.2mm; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .row { display: flex; gap: 2mm; font-size: 2.7mm; line-height: 1.5; }
        .row .k { color: #6b7280; min-width: 15mm; }
        .row .v { font-weight: 600; }
        .foot { padding: 0 4mm 2.6mm; }
        .barcode { height: 9mm; }
        .barcode svg { display: block; width: 100%; height: 100%; }
        .code { text-align: center; font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 2.5mm; letter-spacing: .12em; margin-top: .3mm; }
        @media print {
            body { background: #fff; }
            .bar { display: none; }
            .sheet { padding: 0; gap: 4mm; justify-content: start; }
        }
    </style>
</head>
<body>
    <div class="bar">
        <p>{{ trans_choice('idcard.count', count($cards), ['count' => count($cards)]) }} · {{ __('idcard.print_tip') }}</p>
        <button type="button" onclick="window.print()">{{ __('idcard.print') }}</button>
    </div>

    <main class="sheet">
        @foreach ($cards as $card)
            <section class="card" aria-label="{{ $card['name'] }}">
                <div class="head">
                    <div class="school">{{ $school }}</div>
                    <div class="tag">{{ __('idcard.student_id') }}</div>
                </div>
                <div class="body">
                    <div class="avatar" aria-hidden="true">{{ $card['initials'] }}</div>
                    <div class="info">
                        <p class="name">{{ $card['name'] }}</p>
                        <div class="row"><span class="k">{{ __('idcard.admission') }}</span><span class="v">{{ $card['number'] }}</span></div>
                        @if ($card['class'])
                            <div class="row"><span class="k">{{ __('idcard.class') }}</span><span class="v">{{ $card['class'] }}</span></div>
                        @endif
                        @if ($card['year'])
                            <div class="row"><span class="k">{{ __('idcard.year') }}</span><span class="v">{{ $card['year'] }}</span></div>
                        @endif
                    </div>
                </div>
                <div class="foot">
                    @if ($card['barcode'])
                        <div class="barcode">{!! $card['barcode'] !!}</div>
                    @endif
                    <div class="code">{{ $card['number'] }}</div>
                </div>
            </section>
        @endforeach
    </main>
</body>
</html>
