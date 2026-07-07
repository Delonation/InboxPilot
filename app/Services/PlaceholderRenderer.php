<?php

namespace App\Services;

use App\Models\Contact;

/**
 * Replaces the supported merge tokens in subjects and bodies. This is a strict
 * allow-list string replace: it never evaluates user content as code or Blade,
 * so a template cannot be used to execute anything.
 */
class PlaceholderRenderer
{
    /**
     * @param  array<string, string>  $data  Resolved token => value pairs.
     */
    public function render(string $content, array $data): string
    {
        $replacements = [];

        foreach (config('inboxpilot.placeholders') as $token) {
            $replacements['{{'.$token.'}}'] = $data[$token] ?? '';
            // Tolerate spaced tokens like {{ first_name }}.
            $replacements['{{ '.$token.' }}'] = $data[$token] ?? '';
        }

        return strtr($content, $replacements);
    }

    /**
     * Build the token => value map for a contact. The unsubscribe URL is supplied
     * by the caller (the sender generates a signed, per-recipient link).
     */
    public function dataForContact(Contact $contact, string $unsubscribeUrl): array
    {
        return [
            'first_name' => $contact->first_name ?? '',
            'last_name' => $contact->last_name ?? '',
            'email' => $contact->email,
            'company' => $contact->company ?? '',
            'unsubscribe_url' => $unsubscribeUrl,
        ];
    }

    /** Sample values used for previews so the user sees realistic output. */
    public function sampleData(): array
    {
        return [
            'first_name' => 'Jordan',
            'last_name' => 'Lee',
            'email' => 'jordan.lee@example.com',
            'company' => 'Northwind Co',
            'unsubscribe_url' => '#unsubscribe-preview',
        ];
    }
}
