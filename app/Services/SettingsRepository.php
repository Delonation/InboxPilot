<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Database-backed overrides for the operator-adjustable settings, so the admin
 * Settings page can change them at runtime without editing .env. Values fall
 * back to config()/env defaults when not present. Cached to avoid a query per
 * request; the cache is flushed on save.
 */
class SettingsRepository
{
    private const CACHE_KEY = 'inboxpilot.settings';

    /** Keys managed here, with their config() target and a caster. */
    public const MANAGED = [
        'registration_open' => ['inboxpilot.registration_open', 'bool'],
        'email_verification' => ['inboxpilot.email_verification', 'bool'],
        'send_chunk_size' => ['inboxpilot.send.chunk_size', 'int'],
        'send_delay_ms' => ['inboxpilot.send.delay_ms', 'int'],
    ];

    /** @return array<string, string> */
    public function all(): array
    {
        if (! Schema::hasTable('settings')) {
            return [];
        }

        return Cache::rememberForever(self::CACHE_KEY, fn () => Setting::pluck('value', 'key')->toArray());
    }

    public function set(string $key, ?string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Apply stored overrides onto the live config. Called from a service
     * provider so the rest of the app keeps reading plain config() values.
     */
    public function applyToConfig(): void
    {
        $stored = $this->all();

        foreach (self::MANAGED as $key => [$configKey, $type]) {
            if (! array_key_exists($key, $stored)) {
                continue;
            }

            $value = match ($type) {
                'bool' => filter_var($stored[$key], FILTER_VALIDATE_BOOLEAN),
                'int' => (int) $stored[$key],
                default => $stored[$key],
            };

            config([$configKey => $value]);
        }
    }
}
