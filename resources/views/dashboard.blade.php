<x-layouts.dashboard title="Dashboard">
    @php $smtpReady = $user->smtpReady(); @endphp

    {{-- Setup checklist --}}
    @unless ($setup['complete'])
        <div class="acard" style="margin-bottom:18px;">
            <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between; padding:18px 20px;">
                <div>
                    <div class="acard-title">Finish setting up your account</div>
                    <p style="margin-top:6px; color:var(--muted); font-size:.9rem;">Complete these steps before you can send campaigns.</p>
                    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:12px;">
                        <span class="badge {{ $setup['profile'] ? 'badge-green' : 'badge-gray' }}">01 · Profile</span>
                        <span class="badge {{ $setup['smtp'] ? 'badge-green' : 'badge-gray' }}">02 · SMTP</span>
                        <span class="badge {{ $setup['tested'] ? 'badge-green' : 'badge-gray' }}">03 · Test email</span>
                    </div>
                </div>
                <a href="{{ route('setup.index') }}" class="abtn abtn-accent">Continue setup</a>
            </div>
        </div>
    @endunless

    {{-- Stat strip --}}
    @php
        $rate = $stats['attempted'] > 0 ? (int) round($stats['sent'] / $stats['attempted'] * 100) : 0;
        $segs = [
            ['Contacts', $stats['contacts'], '', number_format($stats['templates']).' templates', route('contacts.index'), false],
            ['Campaigns sent', $stats['campaigns_sent'], '', number_format($stats['sent']).' delivered', route('campaigns.index'), false],
            ['Delivery rate', $rate, '%', number_format($stats['sent']).' / '.number_format($stats['attempted']).' sent', route('campaigns.index'), false],
            ['Failed', $stats['failed'], '', 'across all sends', route('logs.index'), $stats['failed'] > 0],
        ];
    @endphp
    <div class="stat-strip">
        @foreach ($segs as [$label, $value, $suffix, $sub, $href, $amber])
            <a href="{{ $href }}" class="stat-seg">
                <div class="k">{{ $label }}</div>
                <div class="v {{ $amber ? 'amber' : '' }}" data-count="{{ $value }}" data-suffix="{{ $suffix }}">0{{ $suffix }}</div>
                <div class="sub">{{ $sub }}</div>
            </a>
        @endforeach
    </div>

    {{-- Main grid --}}
    <div class="grid-2" style="margin-top:18px;">

        {{-- Recent campaigns --}}
        <div class="acard">
            <div class="acard-head">
                <span class="acard-title">Recent campaigns</span>
                <a href="{{ route('campaigns.index') }}" class="alink">View all</a>
            </div>

            @if ($recentCampaigns->isEmpty())
                <div class="empty-block">
                    <p>No campaigns yet. Compose your first one.</p>
                    <a href="{{ route('campaigns.index') }}" class="abtn abtn-accent">New campaign</a>
                </div>
            @else
                <div class="feed">
                    <div class="feed-rail"></div>
                    @foreach ($recentCampaigns as $c)
                        <div class="feed-entry type-campaign">
                            <span class="feed-node"></span>
                            <a href="{{ route('campaigns.show', $c) }}" class="feed-head" style="text-decoration:none;">
                                <span class="feed-time">{{ $c->created_at->diffForHumans(['short' => true]) }}</span>
                                <span class="feed-label">[campaign]</span>
                                <span class="feed-desc"><strong>{{ $c->name }}</strong> — {{ number_format((int) $c->total_sent) }} sent · {{ number_format((int) $c->total_failed) }} failed</span>
                                <span class="feed-chev"><x-lucide name="chevron-right" /></span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SMTP + errors --}}
        <div class="stack">
            <div class="acard smtp-card {{ $smtpReady ? '' : 'failed' }}">
                <div class="acard-head">
                    <span class="acard-title">SMTP connection</span>
                    <span class="acard-sub">{{ $smtpReady ? 'live' : 'not set up' }}</span>
                </div>
                <div class="smtp-term">
                    @if ($smtpReady)
                        <div class="r"><span class="kk">status</span> <span class="ok">250 OK · connected</span></div>
                        <div class="r"><span class="kk">config</span> <span class="vv">{{ $user->smtpSetting->summary() }}</span></div>
                    @else
                        <div class="r"><span class="bad">No SMTP account connected.</span></div>
                        <div class="r"><span class="kk">next</span> <span class="vv">connect a server to start sending</span></div>
                    @endif
                </div>
                <div class="smtp-foot">
                    <span class="last {{ $smtpReady ? '' : 'bad' }}">{{ $smtpReady ? 'Ready to send' : 'Action required' }}</span>
                    <a href="{{ route('smtp.edit') }}" class="abtn abtn-ghost abtn-sm">{{ $smtpReady ? 'Manage' : 'Set up SMTP' }}</a>
                </div>
            </div>

            <div class="acard">
                <div class="acard-head"><span class="acard-title">Recent sending errors</span></div>
                @forelse ($recentErrors as $err)
                    <div style="padding:12px 18px; border-bottom:1px solid var(--line);">
                        <div class="mono" style="font-size:.78rem; color:var(--text);">{{ $err->email }}</div>
                        <div class="mono" style="font-size:.72rem; color:var(--amber); margin-top:3px;">{{ \Illuminate\Support\Str::limit($err->error_message, 70) }}</div>
                    </div>
                @empty
                    <p class="empty-line">No sending errors.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                document.querySelectorAll('[data-count]').forEach((el) => {
                    const target = parseFloat(el.getAttribute('data-count')) || 0;
                    const suffix = el.getAttribute('data-suffix') || '';
                    const fmt = (n) => Number(n).toLocaleString();
                    if (reduce || target === 0) { el.textContent = fmt(target) + suffix; return; }
                    let start = null;
                    const step = (t) => {
                        if (!start) start = t;
                        const p = Math.min((t - start) / 600, 1);
                        el.textContent = fmt(Math.round(target * (1 - Math.pow(1 - p, 3)))) + suffix;
                        if (p < 1) requestAnimationFrame(step);
                    };
                    requestAnimationFrame(step);
                });
            })();
        </script>
    @endpush
</x-layouts.dashboard>
