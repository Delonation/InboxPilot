<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ActivityLogger;
use App\Services\RecaptchaService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        abort_unless(config('inboxpilot.registration_open'), 403, 'Registration is currently closed.');

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * New accounts are always created as a pending normal user. They can log in
     * but only see the waiting-for-approval screen until an admin approves them.
     *
     * @throws ValidationException
     */
    public function store(Request $request, RecaptchaService $recaptcha): RedirectResponse
    {
        abort_unless(config('inboxpilot.registration_open'), 403, 'Registration is currently closed.');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip())) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Please confirm you are not a robot.',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
            'status' => User::STATUS_PENDING,
        ]);

        // Every user gets an empty profile row up front so the setup wizard and
        // dashboard never have to null-check its existence.
        UserProfile::create([
            'user_id' => $user->id,
            'sender_name' => $user->name,
        ]);

        ActivityLogger::user($user->id, 'registered');

        // Only dispatch the verification email when the operator enabled it.
        if (config('inboxpilot.email_verification')) {
            event(new Registered($user));
        }

        Auth::login($user);

        return redirect()->route('status.pending');
    }
}
