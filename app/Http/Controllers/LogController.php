<?php

namespace App\Http\Controllers;

use App\Models\SmtpLog;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The user-facing activity log: their own actions plus their SMTP attempts.
 * Strictly scoped to the authenticated user.
 */
class LogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $tab = $request->get('tab') === 'smtp' ? 'smtp' : 'activity';

        $logs = $tab === 'smtp'
            ? SmtpLog::where('user_id', $user->id)->latest()->paginate(20)->withQueryString()
            : UserActivityLog::where('user_id', $user->id)->latest()->paginate(20)->withQueryString();

        return view('logs.index', compact('logs', 'tab'));
    }
}
