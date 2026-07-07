<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate the approved-user app. Logged-in users whose account is not approved are
 * routed to the matching status screen instead of seeing any feature. Admins
 * are always allowed (they are approved on creation).
 */
class EnsureUserApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isApproved()) {
            return $next($request);
        }

        return match ($user->status) {
            \App\Models\User::STATUS_REJECTED => redirect()->route('status.rejected'),
            \App\Models\User::STATUS_SUSPENDED => redirect()->route('status.suspended'),
            default => redirect()->route('status.pending'),
        };
    }
}
