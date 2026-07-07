<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Services\SmtpMailer;
use App\Support\SmtpSettingsForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmtpController extends Controller
{
    public function edit(Request $request): View
    {
        return view('smtp.edit', ['smtp' => $request->user()->smtpSetting]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate(SmtpSettingsForm::rules(passwordRequired: ! $user->smtpSetting));

        SmtpSettingsForm::save($user, $data);
        ActivityLogger::user($user->id, 'smtp_updated');

        return redirect()->route('smtp.edit')
            ->with('success', 'SMTP settings saved. Send a test email to confirm the connection works.');
    }

    public function test(Request $request, SmtpMailer $mailer): RedirectResponse
    {
        $user = $request->user();
        $smtp = $user->smtpSetting;

        if (! $smtp) {
            return back()->with('error', 'Add your SMTP settings before sending a test email.');
        }

        $result = $mailer->sendTest($smtp);

        ActivityLogger::smtp($user->id, 'test', $result['success'], $result['response'], $result['error']);

        if ($result['success']) {
            $smtp->forceFill(['last_test_passed_at' => now(), 'last_test_error' => null])->save();

            return back()->with('success', 'Test email sent successfully to '.$smtp->from_email.'. Your SMTP connection is working.');
        }

        $smtp->forceFill(['last_test_error' => $result['error']])->save();

        return back()->with('error', 'Test failed: '.$result['error']);
    }
}
