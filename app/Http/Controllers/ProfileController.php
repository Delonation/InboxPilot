<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'profile' => $request->user()->profile,
        ]);
    }

    /** Update account + sender/company profile details. */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'company_name' => ['nullable', 'string', 'max:255'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'sender_email' => ['nullable', 'email', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
            'timezone' => ['required', 'string', 'max:64'],
        ]);

        // Changing email re-arms verification when the feature is enabled.
        if ($data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email']])->save();

        $user->profile->update([
            'company_name' => $data['company_name'] ?? null,
            'sender_name' => $data['sender_name'] ?? null,
            'sender_email' => $data['sender_email'] ?? null,
            'reply_to_email' => $data['reply_to_email'] ?? null,
            'timezone' => $data['timezone'],
        ]);

        ActivityLogger::user($user->id, 'profile_updated');

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);
        ActivityLogger::user($request->user()->id, 'password_changed');

        return back()->with('success', 'Password updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
