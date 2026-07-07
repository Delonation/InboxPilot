<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignRecipient;
use App\Models\SmtpLog;
use App\Models\SystemLog;
use Illuminate\View\View;

class LogController extends Controller
{
    public function campaigns(): View
    {
        return view('admin.logs.campaigns', [
            'rows' => CampaignRecipient::with('campaign.user')
                ->where('status', CampaignRecipient::STATUS_FAILED)
                ->latest('updated_at')
                ->paginate(25),
        ]);
    }

    public function smtp(): View
    {
        return view('admin.logs.smtp', [
            'rows' => SmtpLog::with('user')->latest()->paginate(25),
        ]);
    }

    public function system(): View
    {
        return view('admin.logs.system', [
            'rows' => SystemLog::latest()->paginate(25),
        ]);
    }
}
