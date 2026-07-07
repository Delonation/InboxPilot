@props(['title' => null])

@php
    $me = auth()->user();
    $smtpReady = $me->smtpReady();

    $nav = [
        [null, [
            ['dashboard', 'dashboard', 'Dashboard', 'dashboard'],
        ]],
        ['Audience', [
            ['contacts.index', 'users', 'Contacts', 'contacts.*'],
        ]],
        ['Campaigns', [
            ['templates.index', 'file-text', 'Templates', 'templates.*'],
            ['campaigns.index', 'send', 'Campaigns', 'campaigns.*'],
        ]],
        ['Deliverability', [
            ['smtp.edit', 'server', 'SMTP settings', 'smtp.*'],
            ['domain-health.index', 'globe', 'Domain health', 'domain-health.*'],
            ['logs.index', 'list', 'Activity log', 'logs.*'],
        ]],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.admin-head')
</head>
<body>
<div class="admin" :class="{ 'is-collapsed': collapsed, 'is-drawer': drawer }"
     x-data="{ collapsed: false, drawer: false }" @keydown.window.escape="drawer = false">

    <div class="scrim" @click="drawer = false"></div>

    {{-- Sidebar --}}
    <aside class="side">
        <a href="{{ route('dashboard') }}" class="side-brand">
            <img src="{{ asset('Logo_inbox_flight.png') }}" alt="">
            <b>{{ config('app.name') }}</b>
        </a>

        <nav class="side-nav">
            @unless ($me->canSend())
                <div class="side-section">
                    <a href="{{ route('setup.index') }}" data-tip="Setup"
                       class="side-item {{ request()->routeIs('setup.*') ? 'active' : '' }}">
                        <x-lucide name="wrench" />
                        <span class="label">Finish setup</span>
                    </a>
                </div>
            @endunless

            @foreach ($nav as [$section, $items])
                <div class="side-section">
                    @if ($section)<p class="side-section-label">{{ $section }}</p>@endif
                    @foreach ($items as [$route, $icon, $label, $pattern])
                        <a href="{{ route($route) }}" data-tip="{{ $label }}"
                           class="side-item {{ request()->routeIs($pattern) ? 'active' : '' }}">
                            <x-lucide :name="$icon" />
                            <span class="label">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <div class="side-foot">
            @if ($me->isAdmin())
                <a href="{{ route('admin.dashboard') }}" data-tip="Admin panel" class="side-item">
                    <x-lucide name="user-check" />
                    <span class="label">Admin panel</span>
                </a>
            @endif
            <a href="{{ route('profile.edit') }}" data-tip="Settings"
               class="side-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <x-lucide name="settings" />
                <span class="label">Settings</span>
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="icon-btn hamburger" @click="drawer = true" aria-label="Open menu"><x-lucide name="menu" /></button>
                <button class="icon-btn collapse-btn" @click="collapsed = !collapsed" aria-label="Collapse sidebar"><x-lucide name="panel-left" /></button>
                <h1>{{ $title ?? 'Dashboard' }}</h1>
            </div>

            <div class="topbar-right">
                <a href="{{ route('smtp.edit') }}" class="smtp-pill {{ $smtpReady ? '' : 'bad' }}">
                    <span class="d"></span>{{ $smtpReady ? 'SMTP connected' : 'SMTP not set up' }}
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
                        <a href="{{ route('profile.edit') }}" role="menuitem"><x-lucide name="user" /> Profile &amp; settings</a>
                        @if ($me->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" role="menuitem"><x-lucide name="dashboard" /> Admin</a>
                        @endif
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
                <x-flash />
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<div class="toasts" id="toasts" aria-live="polite" aria-atomic="false"></div>
@stack('scripts')
</body>
</html>
