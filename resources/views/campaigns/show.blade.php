<x-layouts.dashboard title="Campaign report">
    @php
        $attempted = (int) $campaign->total_attempted;
        $rate = $attempted > 0 ? (int) round($campaign->total_sent / $attempted * 100) : 0;
    @endphp

    {{-- Header --}}
    <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-start; justify-content:space-between;">
        <div>
            <h1 style="font-family:var(--f-disp); font-weight:600; font-size:clamp(1.6rem,3vw,2.1rem); letter-spacing:-.02em; line-height:1.1;">{{ $campaign->name }}</h1>
            <p class="mono" style="margin-top:8px; font-size:.74rem; letter-spacing:.02em; color:var(--faint);">
                Sent {{ $campaign->completed_at?->format('M j, Y · g:i a') ?? '—' }}
            </p>
            <div style="margin-top:12px;"><x-status-badge :status="$campaign->status" /></div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('campaigns.recipients', $campaign) }}" class="abtn abtn-ghost">Recipient details</a>
            @if($campaign->isSending())
                <a href="{{ route('campaigns.send', $campaign) }}" class="abtn abtn-accent">Resume sending</a>
            @endif
        </div>
    </div>

    {{-- Stat strip --}}
    @php
        $segs = [
            ['Recipients', $campaign->total_recipients, false],
            ['Sent', $campaign->total_sent, false],
            ['Failed', $campaign->total_failed, $campaign->total_failed > 0],
            ['Skipped', $campaign->total_skipped, false],
        ];
    @endphp
    <div class="stat-strip" style="margin-top:22px;">
        @foreach ($segs as [$label, $value, $amber])
            <div class="stat-seg" style="cursor:default;">
                <div class="k">{{ $label }}</div>
                <div class="v {{ $amber ? 'amber' : '' }}" data-count="{{ $value }}">0</div>
            </div>
        @endforeach
    </div>

    <div class="grid-2" style="margin-top:18px;">
        {{-- Left: delivery breakdown + honesty --}}
        <div class="stack">
            <div class="acard">
                <div class="acard-head">
                    <span class="acard-title">Delivery breakdown</span>
                    <span class="acard-sub">{{ $rate }}% accepted</span>
                </div>

                <div style="padding:16px 18px 6px;">
                    {{-- delivery rate bar --}}
                    <div style="height:8px; border-radius:999px; background:#EFEEE9; overflow:hidden;">
                        <div style="height:100%; width:{{ $rate }}%; background:var(--accent); border-radius:999px;"></div>
                    </div>
                    <div class="mono" style="display:flex; justify-content:space-between; margin-top:8px; font-size:.68rem; color:var(--faint);">
                        <span>{{ number_format($campaign->total_sent) }} accepted</span>
                        <span>{{ number_format($attempted) }} attempted</span>
                    </div>
                </div>

                <div style="padding:8px 18px 16px;">
                    @php
                        $rows = [
                            'sent' => ['Sent', false],
                            'failed' => ['Failed', true],
                            'skipped_unsubscribed' => ['Skipped · unsubscribed', false],
                            'skipped_invalid' => ['Skipped · invalid', false],
                            'pending' => ['Pending', false],
                        ];
                    @endphp
                    @foreach ($rows as $key => [$label, $amber])
                        <div class="rline" style="font-size:.8rem; padding:6px 0;">
                            <span class="rk">{{ $label }}</span>
                            <span class="rlead"></span>
                            <span class="rv {{ $amber && ($breakdown[$key] ?? 0) > 0 ? 'amber' : '' }}">{{ number_format($breakdown[$key] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="acard" style="border-color:rgba(14,124,102,.22); background:var(--accent-soft);">
                <div style="padding:16px 18px; display:flex; gap:12px;">
                    <span style="color:var(--accent); flex:none; margin-top:1px;"><x-lucide name="circle-check" class="lucide" /></span>
                    <div>
                        <div style="font-weight:600; font-size:.9rem; color:var(--accent);">What this report means</div>
                        <p style="margin-top:6px; font-size:.86rem; color:#3E5A52; line-height:1.55;">
                            {{ config('app.name') }} confirms whether your SMTP server accepted each message. It cannot confirm inbox placement, opens, or clicks. Inbox delivery depends on your domain authentication (SPF, DKIM, DMARC) and your SMTP provider's reputation.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: details --}}
        <div class="acard">
            <div class="acard-head"><span class="acard-title">Details</span></div>
            <div style="padding:6px 18px 14px;">
                @php
                    $details = [
                        ['Template', $campaign->template->name ?? '—'],
                        ['Subject', $campaign->effectiveSubject()],
                        ['Sender', $campaign->sender_email],
                        ['SMTP account', $campaign->smtp_summary],
                        ['Started', $campaign->started_at?->format('M j, Y · g:i a') ?? '—'],
                        ['Completed', $campaign->completed_at?->format('M j, Y · g:i a') ?? '—'],
                    ];
                @endphp
                @foreach ($details as [$k, $v])
                    <div style="padding:10px 0; border-bottom:1px solid var(--line);">
                        <div class="mono" style="font-size:.62rem; letter-spacing:.12em; text-transform:uppercase; color:var(--muted);">{{ $k }}</div>
                        <div class="mono" style="margin-top:4px; font-size:.82rem; color:var(--text); word-break:break-word;">{{ $v ?: '—' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                document.querySelectorAll('[data-count]').forEach((el) => {
                    const target = parseFloat(el.getAttribute('data-count')) || 0;
                    const fmt = (n) => Number(n).toLocaleString();
                    if (reduce || target === 0) { el.textContent = fmt(target); return; }
                    let start = null;
                    const step = (t) => {
                        if (!start) start = t;
                        const p = Math.min((t - start) / 600, 1);
                        el.textContent = fmt(Math.round(target * (1 - Math.pow(1 - p, 3))));
                        if (p < 1) requestAnimationFrame(step);
                    };
                    requestAnimationFrame(step);
                });
            })();
        </script>
    @endpush
</x-layouts.dashboard>
