<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Google reCAPTCHA v2 (checkbox) verification.
 *
 * Captcha is only enforced when BOTH keys are configured. This lets local
 * development and first-run installs proceed without keys, while production
 * installs that set the keys get full protection on register + login.
 */
class RecaptchaService
{
    public function enabled(): bool
    {
        return filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }

    public function siteKey(): ?string
    {
        return config('services.recaptcha.site_key');
    }

    /**
     * Verify a submitted reCAPTCHA token. Returns true when captcha is disabled
     * so the caller's flow is unaffected on keyless installs.
     */
    public function verify(?string $token, ?string $ip = null): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        if (blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post(config('services.recaptcha.verify_url'), [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            // Fail closed: if Google is unreachable, treat as failed verification.
            ActivityLogger::system('warning', 'reCAPTCHA verification request failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
