<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\SmtpLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $campaignIds = $user->campaigns()->pluck('id');

        $stats = [
            'contacts' => $user->contacts()->count(),
            'templates' => $user->templates()->count(),
            'campaigns_sent' => $user->campaigns()->whereIn('status', [
                Campaign::STATUS_COMPLETED, Campaign::STATUS_COMPLETED_WITH_ERRORS,
            ])->count(),
            'attempted' => (int) $user->campaigns()->sum('total_attempted'),
            'sent' => (int) $user->campaigns()->sum('total_sent'),
            'failed' => (int) $user->campaigns()->sum('total_failed'),
        ];

        $recentCampaigns = $user->campaigns()->latest()->limit(5)->get();

        $recentErrors = CampaignRecipient::whereIn('campaign_id', $campaignIds)
            ->where('status', CampaignRecipient::STATUS_FAILED)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $recentSmtpErrors = SmtpLog::where('user_id', $user->id)
            ->where('success', false)
            ->latest()
            ->limit(5)
            ->get();

        // Setup checklist state.
        $setup = [
            'profile' => $user->setupComplete() || filled($user->profile?->sender_email),
            'smtp' => (bool) $user->smtpSetting,
            'tested' => $user->smtpReady(),
            'complete' => $user->canSend(),
        ];

        return view('dashboard', compact('user', 'stats', 'recentCampaigns', 'recentErrors', 'recentSmtpErrors', 'setup'));
    }
}
