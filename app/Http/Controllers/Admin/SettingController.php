<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\SettingsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', [
            // Reflect the live (possibly overridden) config values.
            'registrationOpen' => (bool) config('inboxpilot.registration_open'),
            'emailVerification' => (bool) config('inboxpilot.email_verification'),
            'chunkSize' => (int) config('inboxpilot.send.chunk_size'),
            'delayMs' => (int) config('inboxpilot.send.delay_ms'),
            'recaptchaConfigured' => filled(config('services.recaptcha.site_key')) && filled(config('services.recaptcha.secret_key')),
        ]);
    }

    public function update(Request $request, SettingsRepository $settings): RedirectResponse
    {
        $data = $request->validate([
            'registration_open' => ['nullable', 'boolean'],
            'email_verification' => ['nullable', 'boolean'],
            'send_chunk_size' => ['required', 'integer', 'between:1,200'],
            'send_delay_ms' => ['required', 'integer', 'between:0,5000'],
        ]);

        $settings->set('registration_open', $request->boolean('registration_open') ? '1' : '0');
        $settings->set('email_verification', $request->boolean('email_verification') ? '1' : '0');
        $settings->set('send_chunk_size', (string) $data['send_chunk_size']);
        $settings->set('send_delay_ms', (string) $data['send_delay_ms']);

        ActivityLogger::admin($request->user()->id, 'updated_settings');

        return back()->with('success', 'Settings saved.');
    }
}
