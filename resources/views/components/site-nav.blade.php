@props(['variant' => 'full', 'context' => null])

@php $registerOpen = config('inboxpilot.registration_open'); @endphp

@if ($variant === 'auth')
    {{-- Auth pages: logo + a single context switch on the right --}}
    <header class="nav nav-auth" data-scrolled="false" id="nav">
        <div class="container nav-inner">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('Logo_inbox_flight.png') }}" alt="" class="brand-logo">
                {{ config('app.name') }}
            </a>
            <div class="nav-actions">
                @if ($context === 'register')
                    <span class="nav-switch">Already registered? <a href="{{ route('login') }}">Log in</a></span>
                @elseif ($registerOpen)
                    <span class="nav-switch">Need an account? <a href="{{ route('register') }}">Register</a></span>
                @endif
            </div>
        </div>
    </header>
@else
    {{-- Full site nav (landing) --}}
    <header class="nav" x-data="{ open: false }" :data-open="open" data-scrolled="false" id="nav">
        <div class="container nav-inner">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('Logo_inbox_flight.png') }}" alt="" class="brand-logo">
                {{ config('app.name') }}
            </a>
            <nav class="nav-links" aria-label="Primary">
                <a href="{{ url('/') }}#features">Features</a>
                <a href="{{ url('/') }}#demo">Live demo</a>
                <a href="{{ url('/') }}#how">How it works</a>
            </nav>
            <div class="nav-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-ink">Go to dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
                    @if ($registerOpen)<a href="{{ route('register') }}" class="btn btn-ink">Create account</a>@endif
                @endauth
            </div>
            <button class="nav-burger" @click="open = !open" :aria-expanded="open" aria-label="Toggle menu">
                <x-lucide name="menu" x-show="!open" />
                <x-lucide name="x" x-show="open" x-cloak />
            </button>
        </div>
        <div class="nav-mobile">
            <div class="container nav-mobile-inner">
                <a href="{{ url('/') }}#features" class="m-link" @click="open = false">Features</a>
                <a href="{{ url('/') }}#demo" class="m-link" @click="open = false">Live demo</a>
                <a href="{{ url('/') }}#how" class="m-link" @click="open = false">How it works</a>
                <div class="m-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-ink">Go to dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
                        @if ($registerOpen)<a href="{{ route('register') }}" class="btn btn-ink">Create account</a>@endif
                    @endauth
                </div>
            </div>
        </div>
    </header>
@endif

@once
    @push('scripts')
        <script>
            // Navbar: border + shrink on scroll (shared by all site pages)
            (function () {
                const nav = document.getElementById('nav');
                if (!nav) return;
                const onScroll = () => nav.setAttribute('data-scrolled', window.scrollY > 8 ? 'true' : 'false');
                onScroll();
                window.addEventListener('scroll', onScroll, { passive: true });
            })();
        </script>
    @endpush
@endonce
