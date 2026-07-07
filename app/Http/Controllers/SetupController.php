<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Services\SmtpMailer;
use App\Support\SmtpSettingsForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Guided first-time setup. Steps run in order: profile, sender, smtp, test, done.
 * Campaigns stay blocked until the profile is saved AND a test email passes
 * (enforced by User::canSend()).
 */
class SetupController extends Controller
{
    private const STEPS = ['profile', 'sender', 'smtp', 'test', 'done'];

    public function show(Request $request, string $step = 'profile'): View|RedirectResponse
    {
        abort_unless(in_array($step, self::STEPS, true), 404);

        $user = $request->user();

        return view("setup.{$step}", [
            'user' => $user,
            'profile' => $user->profile,
            'smtp' => $user->smtpSetting,
            'steps' => self::STEPS,
            'current' => $step,
            'index' => array_search($step, self::STEPS, true),
        ]);
    }

    public function update(Request $request, string $step, SmtpMailer $mailer): RedirectResponse
    {
        abort_unless(in_array($step, self::STEPS, true), 404);

        $user = $request->user();

        return match ($step) {
            'profile' => $this->saveProfile($request, $user),
            'sender' => $this->saveSender($request, $user),
            'smtp' => $this->saveSmtp($request, $user),
            'test' => $this->sendTest($request, $user, $mailer),
            default => redirect()->route('dashboard'),
        };
    }

    private function saveProfile(Request $request, $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update(['name' => $data['name']]);
        $user->profile->update(['company_name' => $data['company_name'] ?? null]);

        return redirect()->route('setup.index', 'sender');
    }

    private function saveSender(Request $request, $user): RedirectResponse
    {
        $data = $request->validate([
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_email' => ['required', 'email', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
            'timezone' => ['required', 'string', 'max:64'],
        ]);

        $user->profile->update($data + ['setup_completed_at' => $user->profile->setup_completed_at ?? now()]);

        return redirect()->route('setup.index', 'smtp');
    }

    private function saveSmtp(Request $request, $user): RedirectResponse
    {
        $data = $request->validate(SmtpSettingsForm::rules(passwordRequired: ! $user->smtpSetting));
        SmtpSettingsForm::save($user, $data);
        ActivityLogger::user($user->id, 'smtp_updated');

        return redirect()->route('setup.index', 'test');
    }

    private function sendTest(Request $request, $user, SmtpMailer $mailer): RedirectResponse
    {
        $smtp = $user->smtpSetting;

        if (! $smtp) {
            return redirect()->route('setup.index', 'smtp')->with('error', 'Add your SMTP settings first.');
        }

        $result = $mailer->sendTest($smtp);
        ActivityLogger::smtp($user->id, 'test', $result['success'], $result['response'], $result['error']);

        if ($result['success']) {
            $smtp->forceFill(['last_test_passed_at' => now(), 'last_test_error' => null])->save();

            return redirect()->route('setup.index', 'done')->with('success', 'Test email sent. Your account is ready.');
        }

        $smtp->forceFill(['last_test_error' => $result['error']])->save();

        return redirect()->route('setup.index', 'test')->with('error', 'Test failed: '.$result['error']);
    }
}
