<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · Self-hosted email campaigns</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes floaty      { 0%,100% { transform: translateY(0) }      50% { transform: translateY(-12px) } }
        @keyframes floaty-slow { 0%,100% { transform: translateY(0) }      50% { transform: translateY(10px) } }
        @keyframes fadeup      { from { opacity:0; transform: translateY(22px) } to { opacity:1; transform:none } }
        @keyframes blob        { 0%,100% { transform: translate(0,0) scale(1) } 33% { transform: translate(24px,-18px) scale(1.06) } 66% { transform: translate(-18px,16px) scale(.96) } }
        @keyframes sheen       { from { transform: translateX(-120%) } to { transform: translateX(320%) } }
        @keyframes marquee     { from { transform: translateX(0) } to { transform: translateX(-50%) } }
        @keyframes grow        { from { width: 0 } to { width: var(--to, 72%) } }

        .animate-floaty      { animation: floaty 5s ease-in-out infinite; }
        .animate-floaty-slow { animation: floaty-slow 6.5s ease-in-out infinite; }
        .animate-blob        { animation: blob 20s ease-in-out infinite; }
        .fade-up             { opacity: 0; animation: fadeup .7s cubic-bezier(.2,.7,.2,1) forwards; }
        .sheen::after        { content:""; position:absolute; inset:0; width:40%; background:linear-gradient(100deg, transparent, rgba(255,255,255,.55), transparent); animation: sheen 2.4s ease-in-out infinite; }
        .bar-grow            { animation: grow 1.8s cubic-bezier(.2,.7,.2,1) forwards; }
        .marquee-track       { animation: marquee 28s linear infinite; }

        [data-reveal] { opacity:0; transform: translateY(26px); transition: opacity .6s ease, transform .6s ease; }
        [data-reveal].in { opacity:1; transform:none; }

        @media (prefers-reduced-motion: reduce) {
            *, .fade-up, .animate-floaty, .animate-floaty-slow, .animate-blob, .bar-grow, .marquee-track, .sheen::after { animation: none !important; }
            [data-reveal] { opacity:1 !important; transform:none !important; }
        }
    </style>
</head>
<body class="font-sans bg-white text-gray-900 antialiased">

    {{-- ── Announcement banner (dismissible) ───────────────────────────── --}}
    <div x-data="{ show: true }" x-show="show" x-cloak x-transition
         class="relative bg-gray-900 text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-center gap-3 px-4 py-2 text-sm sm:px-6">
            <span class="badge bg-white/10 text-white">New</span>
            <p class="text-gray-200">Bring your own SMTP — no per-email fees, ever.</p>
            <a href="{{ route('login') }}" class="hidden font-medium text-white underline-offset-2 hover:underline sm:inline">Learn more &rarr;</a>
            <button @click="show = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white" aria-label="Dismiss">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>
    </div>

    {{-- ── Sticky nav ──────────────────────────────────────────────────── --}}
    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-gray-100 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3.5 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-900 text-white">
                    <x-icon name="send" class="h-4 w-4" />
                </span>
                <span class="text-base font-semibold text-gray-900">{{ config('app.name') }}</span>
            </a>

            <nav class="hidden items-center gap-8 md:flex">
                <a href="#features" class="text-sm text-gray-600 transition hover:text-gray-900">Features</a>
                <a href="#demo" class="text-sm text-gray-600 transition hover:text-gray-900">Live demo</a>
                <a href="#how" class="text-sm text-gray-600 transition hover:text-gray-900">How it works</a>
            </nav>

            <div class="hidden items-center gap-2 md:flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">Go to dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                    @if(config('inboxpilot.registration_open'))
                        <a href="{{ route('register') }}" class="btn-primary">Create account</a>
                    @endif
                @endauth
            </div>

            <button @click="open = !open" class="btn-ghost md:hidden" aria-label="Menu">
                <x-icon name="menu" class="h-5 w-5" x-show="!open" />
                <x-icon name="x" class="h-5 w-5" x-show="open" x-cloak />
            </button>
        </div>

        {{-- mobile menu --}}
        <div x-show="open" x-cloak x-transition class="border-t border-gray-100 md:hidden">
            <div class="mx-auto max-w-6xl space-y-1 px-4 py-3 sm:px-6">
                <a href="#features" @click="open=false" class="nav-item">Features</a>
                <a href="#demo" @click="open=false" class="nav-item">Live demo</a>
                <a href="#how" @click="open=false" class="nav-item">How it works</a>
                <div class="flex gap-2 pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary w-full">Go to dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary w-full">Log in</a>
                        @if(config('inboxpilot.registration_open'))
                            <a href="{{ route('register') }}" class="btn-primary w-full">Create account</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>
        {{-- ── Hero ────────────────────────────────────────────────────── --}}
        <section class="relative overflow-hidden">
            {{-- floating background blobs --}}
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="animate-blob absolute -left-24 top-0 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
                <div class="animate-blob absolute right-0 top-24 h-96 w-96 rounded-full bg-sky-200/40 blur-3xl" style="animation-delay:-6s"></div>
                <div class="animate-blob absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-violet-200/30 blur-3xl" style="animation-delay:-12s"></div>
            </div>

            <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:py-28">
                {{-- copy --}}
                <div>
                    <span class="fade-up badge-gray inline-flex" style="animation-delay:.05s">
                        <span class="mr-1.5 inline-block h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        Open source · Self-hosted
                    </span>

                    <h1 class="fade-up mt-5 text-4xl font-semibold leading-[1.1] tracking-tight text-gray-900 sm:text-5xl" style="animation-delay:.12s">
                        Send email campaigns from
                        <span class="bg-gradient-to-r from-indigo-600 via-violet-600 to-sky-500 bg-clip-text text-transparent">your own SMTP.</span>
                    </h1>

                    <p class="fade-up mt-5 max-w-xl text-lg text-gray-600" style="animation-delay:.2s">
                        {{ config('app.name') }} is a self-hosted campaign tool. Connect any SMTP account,
                        import contacts, design templates, and send directly — no third-party service,
                        no per-email fees. You keep your data and your deliverability.
                    </p>

                    <div class="fade-up mt-8 flex flex-wrap gap-3" style="animation-delay:.28s">
                        @guest
                            @if(config('inboxpilot.registration_open'))
                                <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 shadow-sm">Get started free</a>
                            @endif
                            <a href="#demo" class="btn-secondary px-5 py-2.5">See it in action</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-primary px-5 py-2.5 shadow-sm">Open dashboard</a>
                        @endguest
                    </div>

                    <div class="fade-up mt-8 flex items-center gap-6 text-sm text-gray-500" style="animation-delay:.36s">
                        <span class="inline-flex items-center gap-1.5"><x-icon name="check-circle" class="h-4 w-4 text-green-500" /> No per-email fees</span>
                        <span class="inline-flex items-center gap-1.5"><x-icon name="shield" class="h-4 w-4 text-green-500" /> Encrypted SMTP</span>
                    </div>
                </div>

                {{-- animated product mockup --}}
                <div class="fade-up relative" style="animation-delay:.24s">
                    {{-- floating chips --}}
                    <div class="animate-floaty absolute -left-5 -top-5 z-20 hidden rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-lg sm:block">
                        <div class="flex items-center gap-2 text-xs font-medium text-gray-700">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100 text-green-600"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                            Delivered
                        </div>
                    </div>
                    <div class="animate-floaty-slow absolute -right-4 -top-4 z-20 hidden rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-lg sm:block" style="animation-delay:-2s">
                        <div class="flex items-center gap-2 text-xs font-medium text-gray-700">
                            <span class="badge-green">SPF</span><span class="badge-green">DKIM</span><span class="badge-green">DMARC</span>
                        </div>
                    </div>
                    <div class="animate-floaty absolute -bottom-5 -left-4 z-20 hidden rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-lg sm:block" style="animation-delay:-3.5s">
                        <div class="flex items-center gap-2 text-xs font-medium text-gray-700">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-900 text-white"><x-icon name="server" class="h-3.5 w-3.5" /></span>
                            SMTP connected
                        </div>
                    </div>

                    {{-- dashboard card --}}
                    <div class="relative rounded-2xl border border-gray-200 bg-white p-5 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-900 text-white"><x-icon name="send" class="h-4 w-4" /></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Summer Campaign</p>
                                    <p class="text-xs text-gray-400">Sending now…</p>
                                </div>
                            </div>
                            <span class="badge-green">Live</span>
                        </div>

                        {{-- progress --}}
                        <div class="mt-5">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Progress</span><span>1,204 / 1,670</span>
                            </div>
                            <div class="relative mt-1.5 h-2.5 overflow-hidden rounded-full bg-gray-100">
                                <div class="sheen relative h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 bar-grow" style="--to:72%"></div>
                            </div>
                        </div>

                        {{-- recipient rows --}}
                        <div class="mt-5 space-y-2">
                            @foreach ([['A','ava@acme.io','Delivered'],['J','jonas@lumen.co','Delivered'],['M','mira@nova.dev','Sending']] as [$i,$mail,$st])
                                <div class="flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600">{{ $i }}</span>
                                        <span class="text-sm text-gray-700">{{ $mail }}</span>
                                    </div>
                                    @if($st === 'Delivered')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600"><x-icon name="check" class="h-3.5 w-3.5" /> {{ $st }}</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600">
                                            <svg class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                                            {{ $st }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- logo marquee --}}
            <div class="border-y border-gray-100 bg-gray-50/60">
                <div class="mx-auto max-w-6xl overflow-hidden px-4 py-6 sm:px-6">
                    <div class="flex w-max marquee-track gap-14 text-sm font-semibold uppercase tracking-wide text-gray-400">
                        @foreach (['Any SMTP','Gmail','Outlook','Hostinger','Amazon SES','Mailgun','Postmark','Any SMTP','Gmail','Outlook','Hostinger','Amazon SES','Mailgun','Postmark'] as $logo)
                            <span class="shrink-0">{{ $logo }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Interactive feature tabs ────────────────────────────────── --}}
        <section id="features" class="mx-auto max-w-6xl px-4 py-20 sm:px-6" data-reveal>
            <div class="mx-auto max-w-2xl text-center">
                <span class="badge-gray">Everything in one place</span>
                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-gray-900">A full campaign toolkit</h2>
                <p class="mt-3 text-gray-600">Click through to see what each part does.</p>
            </div>

            <div x-data="{ tab: 'smtp' }" class="mt-12 grid gap-8 lg:grid-cols-[280px_1fr]">
                {{-- tab buttons --}}
                <div class="space-y-2">
                    @php
                        $tabs = [
                            ['smtp','server','Your SMTP','Bring any provider'],
                            ['contacts','contacts','Contact lists','Import & clean'],
                            ['campaigns','send','Campaigns','Compose & throttle'],
                            ['reports','chart','Honest reports','What was accepted'],
                        ];
                    @endphp
                    @foreach ($tabs as [$key,$icon,$title,$sub])
                        <button @click="tab = '{{ $key }}'"
                                :class="tab === '{{ $key }}' ? 'border-gray-900 bg-gray-900 text-white shadow-sm' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'"
                                class="flex w-full items-center gap-3 rounded-xl border px-4 py-3 text-left transition">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                                  :class="tab === '{{ $key }}' ? 'bg-white/10' : 'bg-gray-100'">
                                <x-icon :name="$icon" class="h-5 w-5" />
                            </span>
                            <span>
                                <span class="block text-sm font-semibold">{{ $title }}</span>
                                <span class="block text-xs opacity-70">{{ $sub }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- tab panels --}}
                <div class="card card-pad min-h-[320px]">
                    @php
                        $panels = [
                            ['smtp','Connect your own SMTP','Bring any SMTP account — Gmail, Outlook, Hostinger, SES, or your own server. Credentials are encrypted at rest and never shown again. We build an isolated transport per send, so one user’s SMTP can never bleed into another.',['Encrypted per-user credentials','SSL (465) & STARTTLS (587)','Automatic connection test']],
                            ['contacts','Import & keep contacts clean','Import by CSV, tag and segment your audience, and keep duplicates, invalid addresses, and unsubscribes out automatically. Your list stays yours.',['CSV import with mapping','Deduplication & validation','Unsubscribes handled for you']],
                            ['campaigns','Compose and send safely','Design templates, preview them, and send straight from your browser with safe, throttled batching so you don’t trip provider limits.',['Reusable templates','Throttled batch sending','Live sending progress']],
                            ['reports','Reports you can trust','See exactly what your SMTP server accepted, with a clear note on what that means — no vanity “open rates” you can’t verify.',['Per-message server responses','Campaign & SMTP logs','Domain health checks']],
                        ];
                    @endphp
                    @foreach ($panels as [$key,$h,$body,$points])
                        <div x-show="tab === '{{ $key }}'" x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" @if(!$loop->first) x-cloak @endif>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $h }}</h3>
                            <p class="mt-3 text-gray-600">{{ $body }}</p>
                            <ul class="mt-5 space-y-2.5">
                                @foreach ($points as $p)
                                    <li class="flex items-center gap-2.5 text-sm text-gray-700">
                                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-green-600"><x-icon name="check" class="h-3 w-3" /></span>
                                        {{ $p }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── Interactive SMTP test demo ──────────────────────────────── --}}
        <section id="demo" class="bg-gray-900" data-reveal>
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2">
                <div>
                    <span class="badge bg-white/10 text-white">Try it</span>
                    <h2 class="mt-4 text-3xl font-semibold tracking-tight text-white">Test your SMTP in one click</h2>
                    <p class="mt-3 max-w-lg text-gray-400">
                        Before a single campaign goes out, {{ config('app.name') }} verifies your connection end-to-end —
                        connect, authenticate, and send a real test — and reports exactly what the server said.
                    </p>
                    <p class="mt-6 text-sm text-gray-500">Hit “Run test” to watch a simulated check &rarr;</p>
                </div>

                {{-- terminal-style interactive demo --}}
                <div x-data="{
                        running: false, done: false,
                        steps: [
                            { label: 'Connecting to smtp.hostinger.com:465', state: 'idle' },
                            { label: 'Securing channel (TLS/SSL)',           state: 'idle' },
                            { label: 'Authenticating credentials',           state: 'idle' },
                            { label: 'Sending test message',                 state: 'idle' },
                            { label: 'Server accepted: 250 OK',              state: 'idle' },
                        ],
                        run() {
                            if (this.running) return;
                            this.running = true; this.done = false;
                            this.steps.forEach(s => s.state = 'idle');
                            let i = 0;
                            const tick = () => {
                                if (i > 0) this.steps[i-1].state = 'done';
                                if (i < this.steps.length) {
                                    this.steps[i].state = 'active';
                                    i++;
                                    setTimeout(tick, 720);
                                } else {
                                    this.running = false; this.done = true;
                                }
                            };
                            setTimeout(tick, 400);
                        }
                     }"
                     class="overflow-hidden rounded-2xl border border-white/10 bg-gray-950 shadow-2xl">
                    <div class="flex items-center gap-2 border-b border-white/10 px-4 py-3">
                        <span class="h-3 w-3 rounded-full bg-red-400/80"></span>
                        <span class="h-3 w-3 rounded-full bg-amber-400/80"></span>
                        <span class="h-3 w-3 rounded-full bg-green-400/80"></span>
                        <span class="ml-2 text-xs text-gray-500">smtp-test</span>
                    </div>

                    <div class="space-y-3 p-5 font-mono text-sm">
                        <template x-for="(step, idx) in steps" :key="idx">
                            <div class="flex items-center gap-3">
                                {{-- status glyph --}}
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center">
                                    <template x-if="step.state === 'done'">
                                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-green-500/20 text-green-400">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        </span>
                                    </template>
                                    <template x-if="step.state === 'active'">
                                        <svg class="h-4 w-4 animate-spin text-indigo-400" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                                    </template>
                                    <template x-if="step.state === 'idle'">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-700"></span>
                                    </template>
                                </span>
                                <span class="transition-colors"
                                      :class="step.state === 'idle' ? 'text-gray-600' : step.state === 'done' ? 'text-gray-300' : 'text-white'"
                                      x-text="step.label"></span>
                            </div>
                        </template>

                        <div class="pt-2">
                            <button @click="run()" :disabled="running"
                                    class="btn w-full bg-indigo-500 text-white hover:bg-indigo-400 focus:ring-indigo-500 disabled:opacity-60">
                                <span x-show="!running && !done">Run test</span>
                                <span x-show="running" x-cloak>Testing…</span>
                                <span x-show="done && !running" x-cloak>✓ Passed — run again</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Animated stats ──────────────────────────────────────────── --}}
        <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6" data-reveal>
            <div class="grid gap-6 sm:grid-cols-3">
                @php
                    $stats = [
                        [100, '%', 'Your data, self-hosted'],
                        [0, '', 'Per-email fees'],
                        [3, 'x', 'Deliverability checks (SPF · DKIM · DMARC)'],
                    ];
                @endphp
                @foreach ($stats as [$target,$suffix,$label])
                    <div class="card card-pad text-center"
                         x-data="{ val: 0 }"
                         x-init="(() => { let s=null,f=0,d=1600; const go=t=>{ if(!s)s=t; const p=Math.min((t-s)/d,1); val=Math.round(f+({{ $target }}-f)*(1-Math.pow(1-p,3))); if(p<1) requestAnimationFrame(go); }; requestAnimationFrame(go); })()">
                        <p class="text-4xl font-semibold tracking-tight text-gray-900">
                            <span x-text="val"></span>{{ $suffix }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── Feature cards (hover glow + staggered reveal) ───────────── --}}
        <section id="how" class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <span class="badge-gray">How it works</span>
                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-gray-900">Live in four steps</h2>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $steps = [
                        ['server','Connect SMTP','Add any SMTP account. We test and encrypt it.'],
                        ['contacts','Import contacts','Upload a CSV. We de-dupe and validate.'],
                        ['document','Design a template','Compose once, reuse across campaigns.'],
                        ['send','Send & track','Throttled sending with honest, per-message results.'],
                    ];
                @endphp
                @foreach ($steps as $idx => [$icon,$title,$copy])
                    <div data-reveal style="transition-delay: {{ $idx * 90 }}ms"
                         class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 transition hover:-translate-y-1 hover:border-gray-300 hover:shadow-lg">
                        <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-100/0 blur-2xl transition-all duration-500 group-hover:bg-indigo-200/60"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-900 text-white transition group-hover:scale-110">
                                    <x-icon :name="$icon" class="h-5 w-5" />
                                </span>
                                <span class="text-2xl font-semibold text-gray-200">0{{ $idx + 1 }}</span>
                            </div>
                            <h3 class="mt-4 text-sm font-semibold text-gray-900">{{ $title }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $copy }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── CTA banner ──────────────────────────────────────────────── --}}
        <section class="mx-auto max-w-6xl px-4 pb-24 sm:px-6" data-reveal>
            <div class="relative overflow-hidden rounded-2xl bg-gray-900 px-8 py-14 text-center shadow-xl">
                <div class="animate-blob pointer-events-none absolute -left-10 -top-10 h-56 w-56 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="animate-blob pointer-events-none absolute -bottom-10 right-0 h-56 w-56 rounded-full bg-violet-500/20 blur-3xl" style="animation-delay:-8s"></div>
                <div class="relative">
                    <h2 class="text-3xl font-semibold tracking-tight text-white">Own your email sending.</h2>
                    <p class="mx-auto mt-3 max-w-xl text-gray-400">
                        Free to register. New accounts are reviewed by an administrator before they can send.
                    </p>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        @guest
                            @if(config('inboxpilot.registration_open'))
                                <a href="{{ route('register') }}" class="btn bg-white px-6 py-2.5 text-gray-900 hover:bg-gray-100 focus:ring-white">Get started free</a>
                            @endif
                            <a href="{{ route('login') }}" class="btn px-6 py-2.5 text-white ring-1 ring-inset ring-white/20 hover:bg-white/10 focus:ring-white/40">Log in</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn bg-white px-6 py-2.5 text-gray-900 hover:bg-gray-100 focus:ring-white">Open dashboard</a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <footer class="border-t border-gray-100">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-8 text-sm text-gray-500 sm:flex-row sm:px-6">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-900 text-white"><x-icon name="send" class="h-3.5 w-3.5" /></span>
                <span class="font-medium text-gray-700">{{ config('app.name') }}</span>
            </div>
            <p class="max-w-md text-center sm:text-right">
                {{ config('app.name') }} confirms whether your SMTP server accepted each message. It cannot guarantee inbox placement.
            </p>
        </div>
    </footer>

    {{-- scroll reveal --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.12 });
            document.querySelectorAll('[data-reveal]').forEach((el) => io.observe(el));
        });
    </script>
</body>
</html>
