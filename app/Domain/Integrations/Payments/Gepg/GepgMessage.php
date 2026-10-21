<?php

namespace App\Domain\Integrations\Payments\Gepg;

/**
 * GePG XML messages (Bill and Payment Information Posting API v4.0): reading a signed payment notice and
 * building the signed acknowledgement. Signatures are Base64 SHA1withRSA over the exact text of the message
 * element, as GePG specifies; the algorithm is GePG's, not our choice.
 */
final class GepgMessage
{
    public const ACK_OK = '7101';

    /** The exact text of the `<gepgPmtSpInfo>` element in the raw body, or null. */
    public static function paymentElement(string $raw): ?string
    {
        return preg_match('~<gepgPmtSpInfo>.*?</gepgPmtSpInfo>~s', $raw, $m) === 1 ? $m[0] : null;
    }

    public static function signature(string $raw): ?string
    {
        return preg_match('~<gepgSignature>\s*([A-Za-z0-9+/=\s]+?)\s*</gepgSignature>~', $raw, $m) === 1 ? preg_replace('/\s+/', '', $m[1]) : null;
    }

    public static function valid(string $element, ?string $signature, string $certificate): bool
    {
        $key = @openssl_pkey_get_public($certificate);
        $raw = $signature === null ? false : base64_decode($signature, true);
        if ($key === false || $raw === false || $raw === '') {
            return false;
        }

        return openssl_verify($element, $raw, $key, OPENSSL_ALGO_SHA1) === 1;
    }

    /**
     * The payments in a notice, as plain text fields; null when the XML is not a payment notice.
     *
     * @return array{sp_code: string, items: list<array<string, string>>}|null
     */
    public static function parse(string $element): ?array
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($element, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
        libxml_use_internal_errors($previous);
        if ($xml === false || ! isset($xml->PymtTrxInf)) {
            return null;
        }

        $items = [];
        foreach ($xml->PymtTrxInf as $trx) {
            $items[] = [
                'control_number' => trim((string) $trx->PayCtrNum),
                'amount' => trim((string) $trx->PaidAmt),
                'currency' => trim((string) $trx->CCy),
                'reference' => trim((string) $trx->PayRefId),
                'sp_code' => trim((string) $trx->SpCode),
                'paid_on' => substr(trim((string) $trx->TrxDtTm), 0, 10),
            ];
        }

        return $items === [] ? null : ['sp_code' => $items[0]['sp_code'], 'items' => $items];
    }

    /** The signed `gepgPmtSpInfoAck` envelope, or null when the private key cannot be used. */
    public static function acknowledgement(string $privateKey, string $code = self::ACK_OK): ?string
    {
        $key = @openssl_pkey_get_private($privateKey);
        $message = '<gepgPmtSpInfoAck><TrxStsCode>'.$code.'</TrxStsCode></gepgPmtSpInfoAck>';
        if ($key === false || ! openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA1)) {
            return null;
        }

        return '<Gepg>'.$message.'<gepgSignature>'.base64_encode($signature).'</gepgSignature></Gepg>';
    }
}
