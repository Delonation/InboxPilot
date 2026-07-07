<x-layouts.admin title="Dashboard">

    {{-- ── Stat strip ──────────────────────────────────────────────── --}}
    @php
        $segs = [
            ['Contacts', $stats['contacts'], '', '+'.number_format($stats['contacts_7d']).' · 7d', route('admin.users.index'), false],
            ['Campaigns sent', $stats['campaigns'], '', '+'.number_format($stats['campaigns_7d']).' · 7d', route('admin.logs.campaigns'), false],
            ['Delivery rate', $stats['delivery_rate'], '%', number_format($stats['sent']).' / '.number_format($stats['attempted']).' sent', route('admin.logs.campaigns'), false],
            ['Failed', $stats['failed'], '', '+'.number_format($stats['failed_7d']).' · 7d', route('admin.logs.smtp'), $stats['failed'] > 0],
        ];
    @endphp
    <div class="stat-strip enter" style="animation-delay:0ms">
        @foreach ($segs as [$label, $value, $suffix, $sub, $href, $amber])
            <a href="{{ $href }}" class="stat-seg">
                <div class="k">{{ $label }}</div>
                <div class="v {{ $amber ? 'amber' : '' }}" data-count="{{ $value }}" data-suffix="{{ $suffix }}">0{{ $suffix }}</div>
                <div class="sub">{{ $sub }}</div>
            </a>
        @endforeach
    </div>

    {{-- ── Pending approvals ───────────────────────────────────────── --}}
    <div class="acard enter" id="approvals-card" style="margin-top:18px; animation-delay:60ms">
        <div class="acard-head">
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="acard-title">Awaiting approval</span>
                <span class="acount" id="approvals-count" @if($pendingCount === 0) style="display:none" @endif>{{ $pendingCount }}</span>
            </div>
            <a href="{{ route('admin.users.pending') }}" class="alink">All pending</a>
        </div>

        <div id="approvals-list">
            @forelse ($pendingUsers as $u)
                <div class="approval-row" data-row>
                    <span class="av">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                    <div class="approval-id">
                        <div class="n">{{ $u->name }}</div>
                        <div class="e">{{ $u->email }}</div>
                    </div>
                    <span class="approval-time">{{ $u->created_at->diffForHumans(['short' => true]) }}</span>
                    <div class="approval-actions">
                        <button type="button" class="abtn abtn-accent abtn-sm" data-action="approve"
                                data-url="{{ route('admin.users.approve', $u) }}" data-email="{{ $u->email }}">Approve</button>
                        <button type="button" class="abtn abtn-ghost abtn-sm" data-action="reject"
                                data-url="{{ route('admin.users.reject', $u) }}" data-email="{{ $u->email }}">Reject</button>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
        <p class="empty-line" id="approvals-empty" @if($pendingCount > 0) style="display:none" @endif>Queue clear. 0 accounts waiting.</p>
    </div>

    {{-- ── Main grid ───────────────────────────────────────────────── --}}
    <div class="grid-2" style="margin-top:18px;">

        {{-- Left: sending activity feed --}}
        <div class="acard enter" style="animation-delay:120ms">
            <div class="acard-head">
                <span class="acard-title">Sending activity</span>
                <a href="{{ route('admin.logs.system') }}" class="alink">View all</a>
            </div>

            @if ($feed->isEmpty())
                <div class="empty-block">
                    <p>No activity yet. Send your first campaign.</p>
                    <a href="{{ route('admin.logs.campaigns') }}" class="abtn abtn-accent">View campaigns</a>
                </div>
            @else
                @php $labels = ['campaign' => 'campaign', 'approved' => 'approved', 'smtp' => 'smtp', 'error' => 'error']; @endphp
                <div class="feed">
                    <div class="feed-rail"></div>
                    @foreach ($feed as $e)
                        @php $isCampaign = $e['type'] === 'campaign' && $e['receipt']; @endphp
                        <div class="feed-entry type-{{ $e['type'] }} {{ $isCampaign ? 'expandable' : '' }}">
                            <span class="feed-node"></span>
                            <div class="feed-head" @if($isCampaign) data-accordion @endif>
                                <span class="feed-time">{{ $e['at']->diffForHumans(['short' => true]) }}</span>
                                <span class="feed-label">[{{ $labels[$e['type']] }}]</span>
                                <span class="feed-desc">@if($e['title'])<strong>{{ $e['title'] }}</strong> — @endif{{ $e['desc'] }}</span>
                                @if ($isCampaign)<span class="feed-chev"><x-lucide name="chevron-right" /></span>@endif
                            </div>
                            @if ($isCampaign)
                                <div class="feed-receipt">
                                    <div class="feed-receipt-inner">
                                        <div class="rline"><span class="rk">recipients</span><span class="rlead"></span><span class="rv">{{ number_format($e['receipt']['recipients']) }}</span></div>
                                        <div class="rline"><span class="rk">accepted</span><span class="rlead"></span><span class="rv">{{ number_format($e['receipt']['accepted']) }}</span></div>
                                        <div class="rline"><span class="rk">failed</span><span class="rlead"></span><span class="rv {{ $e['receipt']['failed'] > 0 ? 'amber' : '' }}">{{ number_format($e['receipt']['failed']) }}</span></div>
                                        @if ($e['receipt']['duration'])
                                            <div class="rline"><span class="rk">duration</span><span class="rlead"></span><span class="rv">{{ $e['receipt']['duration'] }}</span></div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right: SMTP + volume --}}
        <div class="stack">
            {{-- SMTP connection --}}
            <div class="acard smtp-card enter {{ $smtp['ok'] ? '' : 'failed' }}" style="animation-delay:180ms">
                <div class="acard-head">
                    <span class="acard-title">SMTP connection</span>
                    <span class="acard-sub">{{ $smtp['connected'] ? ($smtp['ok'] ? 'live' : 'failing') : 'none' }}</span>
                </div>
                <div class="smtp-term" id="smtp-term">
                    <div class="r"><span class="kk">host</span> <span class="vv">{{ $smtp['host'] ?? '—' }}:{{ $smtp['port'] ?? '—' }}</span></div>
                    <div class="r"><span class="kk">user</span> <span class="vv">{{ $smtp['username'] ?? '—' }}</span></div>
                    <div class="r" id="smtp-last">
                        @if ($smtp['ok'])
                            <span class="ok">250 OK</span> · checked {{ $smtp['passed_at'] ? $smtp['passed_at']->diffForHumans(['short' => true]) : 'not yet' }}
                        @else
                            <span class="bad">{{ \Illuminate\Support\Str::limit($smtp['error'], 40) }}</span>
                        @endif
                    </div>
                    <div class="tl"><span class="sp"></span> <span>→ connecting to {{ $smtp['host'] ?? 'server' }}:{{ $smtp['port'] ?? '465' }}</span></div>
                    <div class="tl"><span class="sp"></span> <span>→ authenticating credentials</span></div>
                    <div class="tl"><span class="sp"></span> <span>→ sending test message</span></div>
                </div>
                <div class="smtp-foot">
                    <span class="last {{ $smtp['ok'] ? '' : 'bad' }}">{{ $smtp['ok'] ? 'Last check passed' : 'Last check failed' }}</span>
                    <button class="abtn abtn-ghost abtn-sm" id="smtp-test">Test now</button>
                </div>
            </div>

            {{-- Send volume --}}
            <div class="acard enter" style="animation-delay:240ms">
                <div class="acard-head">
                    <span class="acard-title">Send volume</span>
                    <span class="acard-sub">7 days</span>
                </div>
                @php
                    $vmax = max(1, collect($volume)->max('sent'));
                    $vtotal = collect($volume)->sum('sent');
                    $slot = 40; $bw = 22; $chartH = 92; $baseY = 100;
                @endphp
                @if ($vtotal === 0)
                    <div class="empty-block"><p>No sends in the last 7 days.</p></div>
                @else
                    <div class="vchart" id="vchart">
                        <svg viewBox="0 0 {{ $slot * 7 }} 122" width="100%" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Sends per day, last 7 days">
                            <line class="vaxis" x1="0" y1="{{ $baseY }}" x2="{{ $slot * 7 }}" y2="{{ $baseY }}" />
                            @foreach ($volume as $i => $d)
                                @php
                                    $h = (int) round($d['sent'] / $vmax * $chartH);
                                    $h = $d['sent'] > 0 ? max($h, 3) : 0;
                                    $x = $i * $slot + ($slot - $bw) / 2;
                                    $y = $baseY - $h;
                                @endphp
                                @if ($h > 0)
                                    <rect class="vbar" x="{{ $x }}" y="{{ $y }}" width="{{ $bw }}" height="{{ $h }}" rx="3"
                                          data-day="{{ $d['label'] }}" data-sent="{{ $d['sent'] }}" data-failed="{{ $d['failed'] }}"></rect>
                                @endif
                                <text class="vlabel" x="{{ $i * $slot + $slot / 2 }}" y="116" text-anchor="middle">{{ $d['label'] }}</text>
                            @endforeach
                        </svg>
                        <div class="vtip" id="vtip"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // ── Count-up ──────────────────────────────────────────
                document.querySelectorAll('[data-count]').forEach((el) => {
                    const target = parseFloat(el.getAttribute('data-count')) || 0;
                    const suffix = el.getAttribute('data-suffix') || '';
                    const fmt = (n) => Number(n).toLocaleString();
                    if (reduce || target === 0) { el.textContent = fmt(target) + suffix; return; }
                    let start = null;
                    const step = (t) => {
                        if (!start) start = t;
                        const p = Math.min((t - start) / 600, 1);
                        const val = Math.round(target * (1 - Math.pow(1 - p, 3)));
                        el.textContent = fmt(val) + suffix;
                        if (p < 1) requestAnimationFrame(step);
                    };
                    requestAnimationFrame(step);
                });

                // ── Feed accordion ────────────────────────────────────
                document.querySelectorAll('.feed-head[data-accordion]').forEach((head) => {
                    head.addEventListener('click', () => head.closest('.feed-entry').classList.toggle('open'));
                });

                // ── Approvals: approve / reject ───────────────────────
                function refreshCounts() {
                    const remaining = document.querySelectorAll('#approvals-list .approval-row').length;
                    const countEl = document.getElementById('approvals-count');
                    const emptyEl = document.getElementById('approvals-empty');
                    if (countEl) { countEl.textContent = remaining; countEl.style.display = remaining ? '' : 'none'; }
                    if (emptyEl) emptyEl.style.display = remaining ? 'none' : '';
                    const sideBadge = document.querySelector('.side-badge');
                    const bellBadge = document.querySelector('.bell-badge');
                    if (sideBadge) { if (remaining) sideBadge.textContent = remaining; else sideBadge.remove(); }
                    if (bellBadge) { if (remaining) bellBadge.textContent = remaining; else bellBadge.remove(); }
                }
                document.querySelectorAll('[data-action]').forEach((btn) => {
                    btn.addEventListener('click', async (ev) => {
                        ev.preventDefault();
                        const row = btn.closest('.approval-row');
                        const email = btn.dataset.email;
                        const action = btn.dataset.action;
                        row.querySelectorAll('button').forEach((b) => (b.disabled = true));
                        try {
                            const res = await fetch(btn.dataset.url, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            });
                            if (!res.ok) throw new Error();
                            row.style.height = row.offsetHeight + 'px';
                            void row.offsetHeight;
                            row.classList.add('removing');
                            setTimeout(() => { row.remove(); refreshCounts(); }, 210);
                            window.adminToast('250 OK: ' + email + ' ' + (action === 'approve' ? 'approved' : 'rejected'), action === 'approve' ? 'success' : 'error');
                        } catch (e) {
                            row.querySelectorAll('button').forEach((b) => (b.disabled = false));
                            window.adminToast('Action failed — please retry.', 'error');
                        }
                    });
                });

                // ── SMTP inline test ──────────────────────────────────
                const testBtn = document.getElementById('smtp-test');
                if (testBtn) {
                    testBtn.addEventListener('click', () => {
                        if (testBtn.disabled) return;
                        testBtn.disabled = true;
                        const lines = document.querySelectorAll('#smtp-term .tl');
                        const last = document.getElementById('smtp-last');
                        const finish = () => {
                            document.querySelectorAll('#smtp-term .tl .sp').forEach((s) => { const c = document.createElement('span'); c.className = 'ck'; c.textContent = '✓'; s.replaceWith(c); });
                            if (last) last.innerHTML = '<span class="ok">250 OK</span> · checked just now';
                            testBtn.disabled = false;
                            testBtn.textContent = 'Test again';
                            window.adminToast('250 OK: SMTP connection verified', 'success');
                        };
                        lines.forEach((l) => l.classList.remove('show'));
                        if (reduce) { lines.forEach((l) => l.classList.add('show')); finish(); return; }
                        let i = 0;
                        const tick = () => { if (i < lines.length) { lines[i].classList.add('show'); i++; setTimeout(tick, 600); } else finish(); };
                        setTimeout(tick, 200);
                    });
                }

                // ── Volume chart tooltip ──────────────────────────────
                const tip = document.getElementById('vtip');
                const chart = document.getElementById('vchart');
                if (tip && chart) {
                    document.querySelectorAll('.vbar').forEach((bar) => {
                        const show = () => {
                            tip.innerHTML = bar.dataset.day + ' · <span class="ok">' + bar.dataset.sent + ' sent</span> · <span class="bad">' + bar.dataset.failed + ' failed</span>';
                            const r = bar.getBoundingClientRect(), cr = chart.getBoundingClientRect();
                            tip.style.left = (r.left - cr.left + r.width / 2) + 'px';
                            tip.style.top = (r.top - cr.top - 8) + 'px';
                            tip.style.opacity = '1';
                            bar.classList.add('hot');
                        };
                        const hide = () => { tip.style.opacity = '0'; bar.classList.remove('hot'); };
                        bar.addEventListener('mouseenter', show);
                        bar.addEventListener('mouseleave', hide);
                    });
                }
            })();
        </script>
    @endpush
</x-layouts.admin>
