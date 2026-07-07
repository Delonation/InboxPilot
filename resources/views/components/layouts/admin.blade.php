@props(['title' => null])

@php
    use App\Models\User;
    $me = auth()->user();
    $pendingCount = User::where('role', User::ROLE_USER)->where('status', User::STATUS_PENDING)->count();

    $setting = $me->smtpSetting;
    if ($setting) {
        $smtpConnected = true;
        $smtpOk = empty($setting->last_test_error);
    } else {
        $smtpConnected = (bool) config('mail.mailers.smtp.host');
        $smtpOk = true;
    }

    $nav = [
        [null, [
            ['admin.dashboard', 'dashboard', 'Dashboard', 'admin.dashboard'],
        ]],
        ['Campaigns', [
            ['admin.logs.campaigns', 'send', 'Campaign logs', 'admin.logs.campaigns'],
        ]],
        ['Deliverability', [
            ['admin.logs.smtp', 'server', 'SMTP logs', 'admin.logs.smtp'],
            ['admin.logs.system', 'list', 'System logs', 'admin.logs.system'],
        ]],
        ['Admin', [
            ['admin.users.pending', 'user-check', 'Approvals', 'admin.users.pending', 'pending'],
            ['admin.users.index', 'users', 'Users', 'admin.users.index'],
            ['admin.settings.edit', 'settings', 'Settings', 'admin.settings.*'],
        ]],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}Admin · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.admin-head')
</head>
<body>
<div class="admin" :class="{ 'is-collapsed': collapsed, 'is-drawer': drawer }"
     x-data="{ collapsed: false, drawer: false }" @keydown.window.escape="drawer = false">

    <div class="scrim" @click="drawer = false"></div>

    {{-- ── Sidebar ─────────────────────────────────────────────────── --}}
    <aside class="side">
        <a href="{{ route('admin.dashboard') }}" class="side-brand">
            <img src="{{ asset('Logo_inbox_flight.png') }}" alt="">
            <b>{{ config('app.name') }}</b>
            <span class="tag">Admin</span>
        </a>

        <nav class="side-nav">
            @foreach ($nav as [$section, $items])
                <div class="side-section">
                    @if ($section)<p class="side-section-label">{{ $section }}</p>@endif
                    @foreach ($items as $item)
                        @php [$route, $icon, $label, $pattern] = $item; $badge = $item[4] ?? null; @endphp
                        <a href="{{ route($route) }}" data-tip="{{ $label }}"
                           class="side-item {{ request()->routeIs($pattern) ? 'active' : '' }}">
                            <x-lucide :name="$icon" />
                            <span class="label">{{ $label }}</span>
                            @if ($badge === 'pending' && $pendingCount > 0)
                                <span class="side-badge">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <div class="side-foot">
            <a href="{{ route('dashboard') }}" data-tip="User dashboard" class="side-item">
                <x-lucide name="dashboard" />
                <span class="label">User dashboard</span>
            </a>
        </div>
    </aside>

    {{-- ── Main ────────────────────────────────────────────────────── --}}
    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="icon-btn hamburger" @click="drawer = true" aria-label="Open menu"><x-lucide name="menu" /></button>
                <button class="icon-btn collapse-btn" @click="collapsed = !collapsed" aria-label="Collapse sidebar"><x-lucide name="panel-left" /></button>
                <h1>{{ $title ?? 'Dashboard' }}</h1>
            </div>

            <div class="topbar-right">
                <span class="smtp-pill {{ $smtpConnected && $smtpOk ? '' : ($smtpConnected ? 'bad' : 'bad') }}">
                    <span class="d"></span>
                    {{ $smtpConnected ? ($smtpOk ? 'SMTP connected' : 'SMTP failing') : 'No SMTP' }}
                </span>

                <a href="{{ route('admin.users.pending') }}" class="icon-btn" aria-label="Pending approvals">
                    <x-lucide name="bell" />
                    @if ($pendingCount > 0)<span class="bell-badge">{{ $pendingCount }}</span>@endif
                </a>

                <div style="position:relative" x-data="{ open: false }" @keydown.escape="open = false">
                    <button class="avatar-btn" @click="open = !open" :aria-expanded="open" aria-haspopup="menu">
                        <span class="avatar">{{ strtoupper(substr($me->name, 0, 1)) }}</span>
                        <span class="avatar-name">{{ \Illuminate\Support\Str::limit($me->name, 14) }}</span>
                    </button>
                    <div class="dropdown" x-show="open" x-cloak @click.outside="open = false" role="menu"
                         x-transition:enter="transition ease-out duration-[120ms]"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-[90ms]"
                         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                        @if (Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" role="menuitem"><x-lucide name="user" /> Profile</a>
                        @endif
                        <a href="{{ route('admin.settings.edit') }}" role="menuitem"><x-lucide name="settings" /> Settings</a>
                        <div class="sep"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"><x-lucide name="log-out" /> Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <div class="content-wrap">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<div class="toasts" id="toasts" aria-live="polite" aria-atomic="false"></div>

<script>
    // Toast helper shared by admin pages
    window.adminToast = function (message, type) {
        const wrap = document.getElementById('toasts');
        if (!wrap) return;
        while (wrap.children.length >= 3) wrap.removeChild(wrap.firstChild);
        const el = document.createElement('div');
        el.className = 'toast' + (type === 'error' ? ' err' : '');
        el.setAttribute('role', 'status');
        el.innerHTML = '<svg class="lucide" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            + (type === 'error'
                ? '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>'
                : '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>')
            + '</svg><span></span>';
        el.querySelector('span').textContent = message;
        wrap.appendChild(el);
        setTimeout(() => { el.style.transition = 'opacity .3s ease'; el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 4000);
    };
</script>
@stack('scripts')
</body>
</html>
