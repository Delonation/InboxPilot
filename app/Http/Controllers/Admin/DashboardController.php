<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\SmtpLog;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $weekAgo = $now->copy()->subDays(7);

        // ── Stat strip ───────────────────────────────────────────────
        $attempted = (int) Campaign::sum('total_attempted');
        $sent = (int) Campaign::sum('total_sent');
        $failed = (int) Campaign::sum('total_failed');

        $stats = [
            'contacts' => Contact::count(),
            'contacts_7d' => Contact::where('created_at', '>=', $weekAgo)->count(),
            'campaigns' => Campaign::count(),
            'campaigns_7d' => Campaign::where('created_at', '>=', $weekAgo)->count(),
            'attempted' => $attempted,
            'sent' => $sent,
            'failed' => $failed,
            'failed_7d' => (int) Campaign::where('completed_at', '>=', $weekAgo)->sum('total_failed'),
            'delivery_rate' => $attempted > 0 ? (int) round($sent / $attempted * 100) : 0,
        ];

        // ── Pending approvals queue ──────────────────────────────────
        $pendingUsers = User::where('role', User::ROLE_USER)
            ->where('status', User::STATUS_PENDING)
            ->latest()
            ->get(['id', 'name', 'email', 'created_at']);
        $pendingCount = $pendingUsers->count();

        // ── Activity feed (merged, most-recent first) ────────────────
        $feed = collect();

        Campaign::with('user')->latest()->limit(6)->get()->each(function ($c) use ($feed) {
            $feed->push([
                'type' => 'campaign',
                'at' => $c->completed_at ?? $c->created_at,
                'title' => $c->name,
                'desc' => number_format((int) $c->total_sent).' of '.number_format((int) $c->total_attempted).' delivered · '.optional($c->user)->email,
                'receipt' => [
                    'recipients' => (int) $c->total_attempted,
                    'accepted' => (int) $c->total_sent,
                    'failed' => (int) $c->total_failed,
                    'duration' => ($c->started_at && $c->completed_at) ? $c->started_at->diffForHumans($c->completed_at, ['parts' => 1, 'short' => true, 'syntax' => true]) : null,
                ],
            ]);
        });

        User::where('role', User::ROLE_USER)
            ->where('status', User::STATUS_APPROVED)
            ->whereNotNull('approved_at')
            ->latest('approved_at')->limit(4)->get()
            ->each(function ($u) use ($feed) {
                $feed->push(['type' => 'approved', 'at' => $u->approved_at, 'title' => null, 'desc' => $u->email.' approved', 'receipt' => null]);
            });

        SmtpLog::with('user')->latest()->limit(4)->get()->each(function ($l) use ($feed) {
            $feed->push([
                'type' => $l->success ? 'smtp' : 'error',
                'at' => $l->created_at,
                'title' => null,
                'desc' => $l->success
                    ? ('SMTP '.($l->context ?? 'check').' ok · '.optional($l->user)->email)
                    : (Str::limit($l->error_message ?? 'SMTP error', 56).' · '.optional($l->user)->email),
                'receipt' => null,
            ]);
        });

        $feed = $feed->filter(fn ($e) => $e['at'])->sortByDesc('at')->take(9)->values();

        // ── SMTP connection (the admin's own SMTP, else operator system) ─
        $setting = auth()->user()->smtpSetting;
        if ($setting) {
            $smtp = [
                'connected' => true,
                'host' => $setting->host,
                'port' => $setting->port,
                'username' => $setting->username,
                'passed_at' => $setting->last_test_passed_at,
                'error' => $setting->last_test_error,
                'ok' => empty($setting->last_test_error),
            ];
        } else {
            $host = config('mail.mailers.smtp.host');
            $smtp = [
                'connected' => (bool) $host,
                'host' => $host,
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'passed_at' => null,
                'error' => null,
                'ok' => true,
            ];
        }

        // ── Send volume (last 7 days) ────────────────────────────────
        $volume = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $date = $day->toDateString();
            $volume[] = [
                'label' => $day->format('D'),
                'date' => $day->format('M j'),
                'sent' => (int) Campaign::whereDate('completed_at', $date)->sum('total_sent'),
                'failed' => (int) Campaign::whereDate('completed_at', $date)->sum('total_failed'),
            ];
        }

        return view('admin.dashboard', compact('stats', 'pendingUsers', 'pendingCount', 'feed', 'volume', 'smtp'));
    }
}
