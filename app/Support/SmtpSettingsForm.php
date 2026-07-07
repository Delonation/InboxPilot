<?php

namespace App\Support;

use App\Models\SmtpSetting;
use App\Models\User;

/**
 * Shared validation rules and persistence for SMTP settings, used by both the
 * setup wizard and the standalone SMTP settings page so the two never drift.
 */
class SmtpSettingsForm
{
    /**
     * @param  bool  $passwordRequired  True on first save; false when editing
     *                                   (a blank password keeps the existing one).
     * @return array<string, mixed>
     */
    public static function rules(bool $passwordRequired): array
    {
        return [
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'encryption' => ['required', 'in:none,ssl,tls'],
            'username' => ['required', 'string', 'max:255'],
            'password' => [$passwordRequired ? 'required' : 'nullable', 'string', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
            'from_email' => ['required', 'email', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * Create or update the user's SMTP settings. A blank password on update
     * leaves the stored (encrypted) password untouched. Any change invalidates
     * the previous "test passed" state so the user must re-test before sending.
     *
     * @param  array<string, mixed>  $data
     */
    public static function save(User $user, array $data): SmtpSetting
    {
        $smtp = $user->smtpSetting ?: new SmtpSetting(['user_id' => $user->id]);

        $smtp->fill([
            'host' => $data['host'],
            'port' => $data['port'],
            'encryption' => $data['encryption'],
            'username' => $data['username'],
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email'],
            'reply_to_email' => $data['reply_to_email'] ?? null,
        ]);

        if (filled($data['password'] ?? null)) {
            $smtp->password_encrypted = $data['password'];
            // New credentials must be re-tested before sending.
            $smtp->last_test_passed_at = null;
        }

        $smtp->save();

        return $smtp;
    }
}
