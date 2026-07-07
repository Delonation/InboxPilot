<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature toggles
    |--------------------------------------------------------------------------
    */

    // Require email verification after registration (uses the system mailer).
    // Admin approval is always required regardless of this setting.
    'email_verification' => (bool) env('MAIL_VERIFICATION_ENABLED', false),

    // Allow public self-registration.
    'registration_open' => (bool) env('REGISTRATION_OPEN', true),

    /*
    |--------------------------------------------------------------------------
    | Campaign sending (browser AJAX chunked sender)
    |--------------------------------------------------------------------------
    | The app has no queue worker or cron on shared hosting. Campaigns are sent
    | from the browser: the sending status page repeatedly POSTs a small batch
    | to the server. Keep the chunk size small so each request finishes well
    | within PHP's max_execution_time. State is persisted to the database after
    | every recipient, so a closed tab pauses (never corrupts) a campaign.
    */
    'send' => [
        // Recipients processed per AJAX batch request.
        'chunk_size' => (int) env('SEND_CHUNK_SIZE', 20),

        // Milliseconds to pause between individual messages within a batch.
        'delay_ms' => (int) env('SEND_DELAY_MS', 400),
    ],

    /*
    |--------------------------------------------------------------------------
    | CSV import
    |--------------------------------------------------------------------------
    */
    'csv' => [
        // Maximum upload size in kilobytes.
        'max_kb' => (int) env('CSV_MAX_KB', 5120),

        // Rows processed per AJAX batch when importing a large file.
        'import_chunk' => 200,
    ],

    /*
    |--------------------------------------------------------------------------
    | DNS health check
    |--------------------------------------------------------------------------
    | DNS-over-HTTPS endpoint (primary). Native dns_get_record() is used as a
    | fallback when PHP DNS functions are available.
    */
    'dns' => [
        'doh_endpoint' => env('DNS_DOH_ENDPOINT', 'https://cloudflare-dns.com/dns-query'),
        'timeout' => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported template placeholders
    |--------------------------------------------------------------------------
    | The keys are the merge tokens (without braces) and map to a contact's
    | resolvable values. {{unsubscribe_url}} is generated per recipient.
    */
    'placeholders' => [
        'first_name',
        'last_name',
        'email',
        'company',
        'unsubscribe_url',
    ],
];
