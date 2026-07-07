<?php

namespace App\Http\Controllers;

use App\Services\DnsHealthChecker;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainHealthController extends Controller
{
    public function index(Request $request): View
    {
        // Pre-fill the domain from the user's sender email when available.
        $default = $request->user()->profile?->sender_email
            ?? $request->user()->smtpSetting?->from_email
            ?? '';

        return view('domain-health.index', [
            'domain' => $default,
            'selector' => null,
            'results' => null,
        ]);
    }

    public function check(Request $request, DnsHealthChecker $checker): View
    {
        $data = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
            'selector' => ['nullable', 'string', 'max:100'],
        ]);

        $results = $checker->check($data['domain'], $data['selector'] ?? null);

        return view('domain-health.index', [
            'domain' => $data['domain'],
            'selector' => $data['selector'] ?? null,
            'results' => $results,
        ]);
    }
}
