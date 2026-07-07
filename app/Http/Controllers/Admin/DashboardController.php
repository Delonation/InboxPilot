<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\SmtpLog;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usersByStatus = User::where('role', User::ROLE_USER)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'users_total' => User::where('role', User::ROLE_USER)->count(),
            'users_pending' => $usersByStatus[User::STATUS_PENDING] ?? 0,
            'users_approved' => $usersByStatus[User::STATUS_APPROVED] ?? 0,
            'users_rejected' => $usersByStatus[User::STATUS_REJECTED] ?? 0,
            'users_suspended' => $usersByStatus[User::STATUS_SUSPENDED] ?? 0,
            'campaigns' => Campaign::count(),
            'attempted' => (int) Campaign::sum('total_attempted'),
            'sent' => (int) Campaign::sum('total_sent'),
            'failed' => (int) Campaign::sum('total_failed'),
        ];

        $recentRegistrations = User::where('role', User::ROLE_USER)->latest()->limit(6)->get();

        $recentCampaignErrors = CampaignRecipient::with('campaign.user')
            ->where('status', CampaignRecipient::STATUS_FAILED)
            ->latest('updated_at')
            ->limit(6)
            ->get();

        $recentSmtpErrors = SmtpLog::with('user')
            ->where('success', false)
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'recentCampaignErrors', 'recentSmtpErrors'));
    }
}
