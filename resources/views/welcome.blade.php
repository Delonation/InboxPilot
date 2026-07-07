<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · Send email campaigns from your own SMTP</title>
    <meta name="description" content="Self-hosted email campaigns. Connect any SMTP account, import contacts, send campaigns. No third-party service, no per-email fees.">

    @include('partials.site-head')
</head>
<body>

@php $registerOpen = config('inboxpilot.registration_open'); @endphp

{{-- ── Announcement bar ─────────────────────────────────────────── --}}
<div class="announce" x-data="{ show: true }" x-show="show" x-cloak>
    <div class="container announce-inner">
        <span class="announce-tag">New</span>
        <span class="announce-text">Bring your own SMTP. No per-email fees, ever.</span>
        <a href="#how" class="announce-link">Learn more</a>
        <button class="announce-x" @click="show = false" aria-label="Dismiss announcement"><x-lucide name="x" /></button>
    </div>
</div>

{{-- ── Navbar (shared component) ────────────────────────────────── --}}
<x-site-nav />

<main>
    {{-- ── Hero ─────────────────────────────────────────────────── --}}
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="badge badge-accent"><span class="dot dot-live"></span> Open source · Self-hosted</span>
                <h1>Send email campaigns from <span class="accent">your own SMTP</span>.</h1>
                <p class="hero-sub">
                    {{ config('app.name') }} connects to any SMTP account. Import contacts, design templates,
                    and send directly. No third-party service, no per-email fees. You keep your data and
                    your deliverability.
                </p>
                <div class="hero-cta">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-accent">Open dashboard</a>
                    @else
                        @if($registerOpen)<a href="{{ route('register') }}" class="btn btn-accent">Get started free</a>@endif
                        <a href="#demo" class="btn btn-outline">See it in action</a>
                    @endauth
                </div>
                <div class="hero-proof">
                    <span><x-lucide name="circle-check" /> No per-email fees</span>
                    <span><x-lucide name="circle-check" /> Encrypted SMTP credentials</span>
                </div>
            </div>

            {{-- Live SMTP session transcript --}}
            <div class="smtp-wrap">
                <div class="smtp">
                    <div class="smtp-bar">
                        <span class="tl tl-r"></span><span class="tl tl-y"></span><span class="tl tl-g"></span>
                        <span class="t">smtp session</span>
                    </div>
                    <div class="smtp-body" id="smtp-session">
                        <div class="smtp-line"><span class="c-cmd">$ inboxpilot send --campaign summer</span></div>
                        <div class="smtp-line"><span class="c-res">220</span> <span class="c-dim">smtp.hostinger.com ESMTP ready</span></div>
                        <div class="smtp-line"><span class="c-cmd">EHLO inboxpilot.local</span></div>
                        <div class="smtp-line"><span class="c-res">250-STARTTLS</span></div>
                        <div class="smtp-line"><span class="c-res">250</span> <span class="c-dim">AUTH LOGIN PLAIN</span></div>
                        <div class="smtp-line"><span class="c-note">→ TLS established (TLS 1.3)</span></div>
                        <div class="smtp-line"><span class="c-note">→ authenticated as hello@dlnwebstudio.com</span></div>
                        <div class="smtp-line"><span class="c-cmd">MAIL FROM:&lt;hello@…&gt;</span>  <span class="c-res">250</span> <span class="c-dim">OK</span></div>
                        <div class="smtp-line"><span class="c-cmd">RCPT TO:&lt;ava@acme.io&gt;</span>  <span class="c-res">250</span> <span class="c-dim">Accepted</span></div>
                        <div class="smtp-line"><span class="c-cmd">DATA …</span>  <span class="c-res">354</span> <span class="c-dim">Go ahead</span></div>
                        <div class="smtp-line"><span class="c-res">250</span> <span class="c-dim">OK: queued as 4Xz91k</span></div>
                        <span class="smtp-cursor" aria-hidden="true"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Config strip ─────────────────────────────────────────── --}}
    <div class="config" aria-label="Supported servers">
        <div class="container">
            <p class="config-cap">Works with any RFC 5321 server</p>
            <p class="config-list">
                smtp.gmail.com:465<span class="sep">·</span>smtp.office365.com:587<span class="sep">·</span>smtp.hostinger.com:465<span class="sep">·</span>email-smtp.us-east-1.amazonaws.com:587<span class="sep">·</span>smtp.mailgun.org:587<span class="sep">·</span>smtp.postmarkapp.com:587
            </p>
        </div>
    </div>

    {{-- ── Feature toolkit ──────────────────────────────────────── --}}
    <section class="section" id="features" data-reveal>
        <div class="container">
            <div class="sec-head">
                <div>
                    <span class="eyebrow">Toolkit</span>
                    <h2 class="h2">A full campaign toolkit</h2>
                    <p class="lede">Four parts, one workflow — from connecting a server to reading back what it accepted.</p>
                </div>
                <span class="sec-anno">§ features / 04 modules</span>
            </div>

            @php
                $tabs = [
                    ['smtp','server','Your SMTP','Bring any provider','Bring any SMTP account','Bring any SMTP account — Gmail, Outlook, Hostinger, Amazon SES, or your own server. Credentials are encrypted at rest and never shown again. We build an isolated transport per send, so one user’s SMTP can never bleed into another.',['Encrypted per-user credentials','SSL (465) and STARTTLS (587)','Automatic connection test']],
                    ['contacts','users','Contact lists','Import and clean','Import contacts and keep them clean','Upload a CSV and InboxPilot validates every row on the way in — checking columns, normalising addresses, and dropping duplicates. Unsubscribes are held in a per-user suppression list and enforced on every send.',['CSV validation with column mapping','Automatic de-duplication','Invalid addresses and unsubscribes filtered']],
                    ['campaigns','send','Campaigns','Compose and throttle','Compose once, throttle the send','Design a template, preview it safely, then send. Sending is throttled per hour so you stay within provider limits, and you can send now or schedule it. Delivery runs in resumable batches — close the tab and it pauses, never corrupts.',['Reusable templates with merge fields','Per-hour throttling','Send now or schedule']],
                    ['reports','bar-chart','Honest reports','What was accepted','Reports you can actually trust','See exactly what your SMTP server said for every message — the raw accept or reject response, per recipient. No invented “open rates” you can’t verify. Accepted means the server took it; nothing more is claimed.',['Per-message server responses','Accepted vs rejected counts','No fake open or click metrics']],
                ];
            @endphp

            <div class="toolkit">
                <div class="tablist" role="tablist" aria-label="Campaign toolkit" aria-orientation="vertical">
                    @foreach ($tabs as $i => [$key,$icon,$title,$sub,$h,$body,$points])
                        <button class="tab" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                                aria-selected="{{ $i === 0 ? 'true' : 'false' }}" tabindex="{{ $i === 0 ? '0' : '-1' }}">
                            <span class="tab-ico"><x-lucide :name="$icon" /></span>
                            <span><b>{{ $title }}</b><small>{{ $sub }}</small></span>
                        </button>
                    @endforeach
                </div>
                <div class="panels">
                    @foreach ($tabs as $i => [$key,$icon,$title,$sub,$h,$body,$points])
                        <div class="panel" role="tabpanel" id="panel-{{ $key }}" aria-labelledby="tab-{{ $key }}" tabindex="0" @if($i !== 0) hidden @endif>
                            <h3>{{ $h }}</h3>
                            <p>{{ $body }}</p>
                            <ul>
                                @foreach ($points as $pt)
                                    <li><span class="tick"><x-lucide name="check" /></span> {{ $pt }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── Signature: SMTP test terminal ────────────────────────── --}}
    <section class="section dark term-section" id="demo" data-reveal>
        <span class="term-code c1" aria-hidden="true">250</span>
        <span class="term-code c2" aria-hidden="true">354</span>
        <span class="term-code c3" aria-hidden="true">221</span>
        <div class="container term-grid">
            <div class="term-copy">
                <span class="eyebrow">Try it</span>
                <h2 class="h2" style="margin-top:12px;">Test your SMTP in one click</h2>
                <p>Before a single campaign goes out, {{ config('app.name') }} verifies your connection end to end. Connect, authenticate, send a real test, and read back exactly what the server said.</p>
                <p class="term-anno">§ smtp · verify connection</p>
            </div>

            <div x-data="{
                    running: false, done: false,
                    steps: [
                        { label: 'Connecting to smtp.hostinger.com:465', state: 'idle', success: false },
                        { label: 'Securing channel (TLS/SSL)',           state: 'idle', success: false },
                        { label: 'Authenticating credentials',           state: 'idle', success: false },
                        { label: 'Sending test message',                 state: 'idle', success: false },
                        { label: 'Server accepted: 250 OK',              state: 'idle', success: true  },
                    ],
                    reset() { this.steps.forEach(s => s.state = 'idle'); },
                    run() {
                        if (this.running) return;
                        this.done = false;
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { this.steps.forEach(s => s.state = 'done'); this.done = true; return; }
                        this.running = true; this.reset();
                        let i = 0;
                        const tick = () => {
                            if (i > 0) this.steps[i-1].state = 'done';
                            if (i < this.steps.length) { this.steps[i].state = 'active'; i++; setTimeout(tick, 720); }
                            else { this.running = false; this.done = true; }
                        };
                        setTimeout(tick, 350);
                    }
                 }" class="terminal">
                <div class="term-bar">
                    <span class="tl tl-r"></span><span class="tl tl-y"></span><span class="tl tl-g"></span>
                    <span class="term-title">smtp-test</span>
                </div>
                <div class="term-body">
                    <template x-for="(step, idx) in steps" :key="idx">
                        <div class="tline" :class="step.success && 'success'" :data-state="step.state">
                            <span class="glyph">
                                <template x-if="step.state === 'done'"><span class="ok"><x-lucide name="check" /></span></template>
                                <template x-if="step.state === 'active'"><span class="spinner"></span></template>
                                <template x-if="step.state === 'idle'"><span class="idle"></span></template>
                            </span>
                            <span class="txt"><span x-text="(step.state==='idle'?'→ ':'')+step.label"></span></span>
                        </div>
                    </template>
                    <div class="term-run">
                        <button class="btn" @click="run()" :disabled="running">
                            <span x-show="!running && !done">Run test</span>
                            <span x-show="running" x-cloak>Running…</span>
                            <span x-show="done && !running" x-cloak>Run again</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Server-receipt ledger ────────────────────────────────── --}}
    <section class="section" data-reveal>
        <div class="container">
            <div class="sec-head">
                <div>
                    <span class="eyebrow">Receipt</span>
                    <h2 class="h2">The whole product, itemised</h2>
                </div>
                <span class="sec-anno">§ receipt / 06 lines</span>
            </div>
            <div class="ledger" style="margin-top:36px;">
                @php
                    $ledger = [
                        ['Data ownership', 'self-hosted, yours', false],
                        ['Per-email fees', '0', true],
                        ['Deliverability checks', 'SPF · DKIM · DMARC', false],
                        ['Credentials', 'AES-256 at rest', false],
                        ['Transport', 'isolated per send', false],
                        ['Source', 'open, auditable', false],
                    ];
                @endphp
                @foreach ($ledger as [$k,$v,$hl])
                    <div class="ledger-row">
                        <span class="k">{{ $k }}</span>
                        <span class="lead" aria-hidden="true"></span>
                        <span class="v">@if($hl)<span class="hl">{{ $v }}</span>@else{{ $v }}@endif</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Session timeline ─────────────────────────────────────── --}}
    <section class="section" id="how" style="padding-top:0;">
        <div class="container">
            <div class="sec-head" data-reveal>
                <div>
                    <span class="eyebrow">Session</span>
                    <h2 class="h2">Live in four steps</h2>
                </div>
                <span class="sec-anno">§ session / 04 steps</span>
            </div>

            <div class="timeline">
                <div class="tl-rail" aria-hidden="true"></div>
                @php
                    $steps = [
                        ['connect','Add any SMTP account. We test and encrypt it.'],
                        ['import','Upload a CSV. We de-dupe and validate.'],
                        ['compose','Write once, reuse across campaigns.'],
                        ['send','Throttled delivery with per-message server responses.'],
                    ];
                @endphp
                @foreach ($steps as $i => [$marker,$text])
                    <div class="tl-entry" data-reveal style="transition-delay: {{ $i * 150 }}ms">
                        <span class="tl-node" aria-hidden="true"></span>
                        <code class="tl-marker">[{{ $marker }}]</code>
                        <span class="tl-text">{{ $text }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Final CTA: full-width dark band ──────────────────────── --}}
    <section class="cta-band dark" data-reveal>
        <div class="container cta-inner">
            <div>
                <h2>Own your email sending.</h2>
                <p>Free to register. New accounts are reviewed by an administrator before they can send.</p>
                <div class="cta-btns">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-light">Open dashboard</a>
                    @else
                        @if($registerOpen)<a href="{{ route('register') }}" class="btn btn-accent">Get started free</a>@endif
                        <a href="{{ route('login') }}" class="btn btn-outline-light">Log in</a>
                    @endauth
                </div>
            </div>
            <p class="cta-sign"><span class="code">221 Bye</span> — but your data stays with you.</p>
        </div>
    </section>
</main>

{{-- ── Footer (shared component) ────────────────────────────────── --}}
<x-site-footer />

@stack('scripts')

<script>
    // Hero: type out the SMTP session line by line
    (function () {
        const body = document.getElementById('smtp-session');
        if (!body) return;
        const lines = body.querySelectorAll('.smtp-line');
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            lines.forEach(l => l.classList.add('on')); return;
        }
        let i = 0;
        const step = () => { if (i < lines.length) { lines[i].classList.add('on'); i++; setTimeout(step, 155); } };
        setTimeout(step, 300);
    })();

    // Feature toolkit: accessible ARIA tablist with roving arrow-key navigation
    (function () {
        const tabs = Array.from(document.querySelectorAll('.tab[role="tab"]'));
        if (!tabs.length) return;
        const panels = tabs.map(t => document.getElementById(t.getAttribute('aria-controls')));
        function select(idx, focus) {
            tabs.forEach((tab, i) => {
                const on = i === idx;
                tab.setAttribute('aria-selected', on ? 'true' : 'false');
                tab.tabIndex = on ? 0 : -1;
                panels[i].hidden = !on;
            });
            if (focus) tabs[idx].focus();
        }
        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => select(i, false));
            tab.addEventListener('keydown', (e) => {
                const last = tabs.length - 1;
                let next = null;
                if (e.key === 'ArrowDown' || e.key === 'ArrowRight') next = i === last ? 0 : i + 1;
                else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') next = i === 0 ? last : i - 1;
                else if (e.key === 'Home') next = 0;
                else if (e.key === 'End') next = last;
                if (next !== null) { e.preventDefault(); select(next, true); }
            });
        });
    })();

    // Reveal on scroll
    (function () {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            els.forEach(el => el.classList.add('in')); return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold: 0.12 });
        els.forEach(el => io.observe(el));
    })();
</script>
</body>
</html>
