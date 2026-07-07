<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Checks the email-authentication DNS records for a domain.
 *
 * Primary lookup is DNS-over-HTTPS (works even where shared hosts disable PHP's
 * DNS functions). If the DoH request fails it falls back to dns_get_record().
 * Missing records never block sending; they are reported as warnings.
 */
class DnsHealthChecker
{
    /**
     * @return array<string, array{status: string, value: ?string, fix: ?string}>
     */
    public function check(string $domain, ?string $dkimSelector = null): array
    {
        $domain = $this->normaliseDomain($domain);

        return [
            'mx' => $this->checkMx($domain),
            'spf' => $this->checkSpf($domain),
            'dmarc' => $this->checkDmarc($domain),
            'dkim' => $this->checkDkim($domain, $dkimSelector),
        ];
    }

    private function checkMx(string $domain): array
    {
        $records = $this->query($domain, 'MX');

        if (empty($records)) {
            return $this->result('missing', null, 'No MX record found. Add an MX record so the domain can receive mail and to improve sender legitimacy.');
        }

        return $this->result('found', implode(', ', array_slice($records, 0, 3)), null);
    }

    private function checkSpf(string $domain): array
    {
        $txt = $this->query($domain, 'TXT');
        $spf = array_values(array_filter($txt, fn ($r) => stripos($r, 'v=spf1') === 0));

        if (empty($spf)) {
            return $this->result('missing', null, 'No SPF record. Add a TXT record starting with "v=spf1" that authorises your sending servers.');
        }

        if (count($spf) > 1) {
            return $this->result('warning', $spf[0], 'Multiple SPF records found. A domain must have exactly one SPF record. Merge them into a single "v=spf1" record.');
        }

        return $this->result('found', $spf[0], null);
    }

    private function checkDmarc(string $domain): array
    {
        $txt = $this->query('_dmarc.'.$domain, 'TXT');
        $dmarc = array_values(array_filter($txt, fn ($r) => stripos($r, 'v=DMARC1') === 0));

        if (empty($dmarc)) {
            return $this->result('missing', null, 'No DMARC record. Add a TXT record at _dmarc.'.$domain.' starting with "v=DMARC1" (start with p=none to monitor).');
        }

        if (stripos($dmarc[0], 'p=none') !== false) {
            return $this->result('warning', $dmarc[0], 'DMARC policy is p=none (monitor only). Once your SPF and DKIM are aligned, move to p=quarantine or p=reject.');
        }

        return $this->result('found', $dmarc[0], null);
    }

    private function checkDkim(string $domain, ?string $selector): array
    {
        if (blank($selector)) {
            return $this->result('not_checked', null, 'DKIM needs a selector to look up. Enter the selector from your SMTP provider (for example "default" or "google") to check it.');
        }

        $txt = $this->query($selector.'._domainkey.'.$domain, 'TXT');
        $dkim = array_values(array_filter($txt, fn ($r) => stripos($r, 'v=DKIM1') !== false || stripos($r, 'p=') !== false));

        if (empty($dkim)) {
            return $this->result('missing', null, 'No DKIM record found for selector "'.$selector.'". Check the selector value, or publish the DKIM key your provider gave you.');
        }

        return $this->result('found', \Illuminate\Support\Str::limit($dkim[0], 80), null);
    }

    /**
     * Query DNS for a name/type. DoH first, PHP fallback second.
     *
     * @return array<int, string>
     */
    private function query(string $name, string $type): array
    {
        $records = $this->queryDoh($name, $type);

        if ($records === null) {
            $records = $this->queryNative($name, $type);
        }

        return $records ?? [];
    }

    /** @return array<int, string>|null  null signals the DoH request itself failed. */
    private function queryDoh(string $name, string $type): ?array
    {
        try {
            $response = Http::timeout(config('inboxpilot.dns.timeout'))
                ->withHeaders(['Accept' => 'application/dns-json'])
                ->get(config('inboxpilot.dns.doh_endpoint'), ['name' => $name, 'type' => $type]);

            if (! $response->ok()) {
                return null;
            }

            $answers = $response->json('Answer') ?? [];

            return array_values(array_map(
                fn ($a) => trim((string) ($a['data'] ?? ''), '"'),
                array_filter($answers, fn ($a) => isset($a['data']))
            ));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** @return array<int, string> */
    private function queryNative(string $name, string $type): array
    {
        if (! function_exists('dns_get_record')) {
            return [];
        }

        try {
            $const = match ($type) {
                'MX' => DNS_MX,
                'TXT' => DNS_TXT,
                default => DNS_A,
            };

            $records = @dns_get_record($name, $const) ?: [];

            return array_values(array_map(function ($r) use ($type) {
                if ($type === 'MX') {
                    return ($r['pri'] ?? '').' '.($r['target'] ?? '');
                }

                return $r['txt'] ?? ($r['target'] ?? '');
            }, $records));
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function normaliseDomain(string $domain): string
    {
        $domain = trim($domain);

        if (str_contains($domain, '@')) {
            $domain = substr(strrchr($domain, '@'), 1);
        }

        return strtolower(preg_replace('#^https?://#', '', $domain));
    }

    private function result(string $status, ?string $value, ?string $fix): array
    {
        return ['status' => $status, 'value' => $value, 'fix' => $fix];
    }
}
