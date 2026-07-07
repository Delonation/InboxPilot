<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Renders the holding screens for accounts that cannot use the app yet. Each
 * method guards against a mismatched status so a user always sees the screen
 * that matches their real account state.
 */
class AccountStatusController extends Controller
{
    public function pending(Request $request): View|RedirectResponse
    {
        return $this->guard($request, User::STATUS_PENDING)
            ?? view('status.pending');
    }

    public function rejected(Request $request): View|RedirectResponse
    {
        return $this->guard($request, User::STATUS_REJECTED)
            ?? view('status.rejected');
    }

    public function suspended(Request $request): View|RedirectResponse
    {
        return $this->guard($request, User::STATUS_SUSPENDED)
            ?? view('status.suspended', ['reason' => $request->user()->suspension_reason]);
    }

    /**
     * If the user's status does not match the requested screen, send them to the
     * right place (their own status screen, or the dashboard if approved).
     */
    protected function guard(Request $request, string $expected): ?RedirectResponse
    {
        $user = $request->user();

        if ($user->status === $expected) {
            return null;
        }

        if ($user->isApproved()) {
            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        return match ($user->status) {
            User::STATUS_REJECTED => redirect()->route('status.rejected'),
            User::STATUS_SUSPENDED => redirect()->route('status.suspended'),
            default => redirect()->route('status.pending'),
        };
    }
}
