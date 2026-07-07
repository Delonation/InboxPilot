<x-layouts.admin title="Settings">
    <x-page-header title="Settings" subtitle="Operator controls for this InboxPilot instance." />

    <div class="max-w-2xl space-y-6">
        <x-card title="Registration &amp; access">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <label class="flex items-start justify-between gap-4">
                    <span>
                        <span class="block text-sm font-medium text-gray-900">Allow public registration</span>
                        <span class="block text-xs text-gray-500">When off, no new accounts can be created.</span>
                    </span>
                    <input type="checkbox" name="registration_open" value="1" @checked($registrationOpen) class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                </label>

                <label class="flex items-start justify-between gap-4 border-t border-gray-100 pt-4">
                    <span>
                        <span class="block text-sm font-medium text-gray-900">Require email verification</span>
                        <span class="block text-xs text-gray-500">Uses the system mailer. Admin approval is always required regardless.</span>
                    </span>
                    <input type="checkbox" name="email_verification" value="1" @checked($emailVerification) class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                </label>

                <div class="grid gap-4 border-t border-gray-100 pt-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="send_chunk_size">Send chunk size</label>
                        <input id="send_chunk_size" name="send_chunk_size" type="number" min="1" max="200" class="form-input" value="{{ $chunkSize }}" required />
                        <p class="form-hint">Recipients per AJAX batch. Smaller is safer on shared hosting.</p>
                        <x-input-error :messages="$errors->get('send_chunk_size')" class="mt-1" />
                    </div>
                    <div>
                        <label class="form-label" for="send_delay_ms">Delay between messages (ms)</label>
                        <input id="send_delay_ms" name="send_delay_ms" type="number" min="0" max="5000" class="form-input" value="{{ $delayMs }}" required />
                        <p class="form-hint">Pause between each email to respect provider rate limits.</p>
                        <x-input-error :messages="$errors->get('send_delay_ms')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Save settings</button>
                </div>
            </form>
        </x-card>

        <x-card title="reCAPTCHA">
            @if($recaptchaConfigured)
                <span class="badge-green"><x-icon name="check" class="h-3 w-3" /> Configured</span>
                <p class="mt-2 text-sm text-gray-500">reCAPTCHA v2 is active on register and login.</p>
            @else
                <span class="badge-amber">Not configured</span>
                <p class="mt-2 text-sm text-gray-500">Set RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY in your .env to enable captcha.</p>
            @endif
        </x-card>

        <x-card title="System mailer">
            <p class="text-sm text-gray-500">
                Transactional email (password reset, optional verification) uses the SMTP account configured in
                your <code class="rounded bg-gray-100 px-1">.env</code>. Per-user campaign SMTP is separate and encrypted.
            </p>
            <p class="mt-2 text-sm text-gray-700">Current mailer: <span class="font-medium">{{ config('mail.default') }}</span></p>
        </x-card>
    </div>
</x-layouts.admin>
