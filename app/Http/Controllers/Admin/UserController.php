<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', User::ROLE_USER)->latest();

        if ($search = trim((string) $request->get('q'))) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function pending(): View
    {
        return view('admin.users.pending', [
            'users' => User::where('role', User::ROLE_USER)
                ->where('status', User::STATUS_PENDING)
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(User $user): View
    {
        abort_if($user->isAdmin(), 404);

        $user->loadCount(['contacts', 'campaigns']);

        $stats = [
            'contacts' => $user->contacts_count,
            'campaigns' => $user->campaigns_count,
            'sent' => (int) $user->campaigns()->sum('total_sent'),
            'failed' => (int) $user->campaigns()->sum('total_failed'),
        ];

        return view('admin.users.show', [
            'user' => $user,
            'profile' => $user->profile,
            'smtpConnected' => (bool) $user->smtpSetting, // boolean only, never the secret
            'stats' => $stats,
        ]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $this->transition($request, $user, User::STATUS_APPROVED, [
            'approved_at' => now(), 'approved_by' => $request->user()->id,
            'rejected_at' => null, 'rejected_by' => null,
            'suspended_at' => null, 'suspended_by' => null, 'suspension_reason' => null,
        ], 'approved');

        return back()->with('success', "{$user->name} approved.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->transition($request, $user, User::STATUS_REJECTED, [
            'rejected_at' => now(), 'rejected_by' => $request->user()->id,
        ], 'rejected');

        return back()->with('success', "{$user->name} rejected.");
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);

        $this->transition($request, $user, User::STATUS_SUSPENDED, [
            'suspended_at' => now(), 'suspended_by' => $request->user()->id,
            'suspension_reason' => $data['reason'] ?? null,
        ], 'suspended', $data['reason'] ?? null);

        return back()->with('success', "{$user->name} suspended.");
    }

    public function reactivate(Request $request, User $user): RedirectResponse
    {
        $this->transition($request, $user, User::STATUS_APPROVED, [
            'suspended_at' => null, 'suspended_by' => null, 'suspension_reason' => null,
            'approved_at' => $user->approved_at ?? now(), 'approved_by' => $user->approved_by ?? $request->user()->id,
        ], 'reactivated');

        return back()->with('success', "{$user->name} reactivated.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        ActivityLogger::admin($request->user()->id, 'deleted_user', $user->id, $user->email);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    /**
     * Apply a status change + audit log. Admins can never be acted upon here.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function transition(Request $request, User $user, string $status, array $attributes, string $action, ?string $details = null): void
    {
        abort_if($user->isAdmin(), 403);

        $user->update($attributes + ['status' => $status]);
        ActivityLogger::admin($request->user()->id, $action, $user->id, $details);
    }
}
