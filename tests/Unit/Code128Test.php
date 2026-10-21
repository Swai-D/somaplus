<?php

use App\Support\Code128;

it('encodes subset B exactly like an independent Code 128 encoder', function () {
    // Start B, A - 0 0 1, check symbol 51, stop: checked against python-barcode's pattern table.
    expect(Code128::modules('A-001'))->toBe('110100100001010001100010011011100100111011001001110110010011100110110111010001100011101011');
});

it('refuses text it cannot write', function (string $text) {
    expect(Code128::modules($text))->toBeNull()->and(Code128::svg($text))->toBeNull();
})->with(['empty' => [''], 'accent' => ['Mwanafunzié'], 'new line' => ["A\n1"], 'too long' => [str_repeat('A', 41)]]);

it('draws a stretchable SVG with only bars, and escapes the text it carries', function () {
    $svg = Code128::svg('A&B"', 30);

    expect($svg)->toStartWith('<svg')->toContain('preserveAspectRatio="none"')->toContain('aria-label="A&amp;B&quot;"')->not->toContain('A&B"');
});
