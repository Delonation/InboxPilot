@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans">
    @php($user = auth()->user())
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        {{-- Mobile backdrop --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-gray-100 px-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-900 text-white">
                    <x-icon name="send" class="h-4 w-4" />
                </span>
                <span class="text-base font-semibold text-gray-900">{{ config('app.name') }}</span>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <x-nav-item href="{{ route('dashboard') }}" icon="home" :active="request()->routeIs('dashboard')">Dashboard</x-nav-item>

                @unless($user->canSend())
                    <x-nav-item href="{{ route('setup.index') }}" icon="wrench" :active="request()->routeIs('setup.*')">Setup</x-nav-item>
                @endunless

                <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-gray-400">Audience</p>
                <x-nav-item href="{{ route('contacts.index') }}" icon="contacts" :active="request()->routeIs('contacts.*')">Contacts</x-nav-item>

                <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-gray-400">Campaigns</p>
                <x-nav-item href="{{ route('templates.index') }}" icon="document" :active="request()->routeIs('templates.*')">Templates</x-nav-item>
                <x-nav-item href="{{ route('campaigns.index') }}" icon="send" :active="request()->routeIs('campaigns.*')">Campaigns</x-nav-item>

                <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-gray-400">Deliverability</p>
                <x-nav-item href="{{ route('smtp.edit') }}" icon="server" :active="request()->routeIs('smtp.*')">SMTP settings</x-nav-item>
                <x-nav-item href="{{ route('domain-health.index') }}" icon="globe" :active="request()->routeIs('domain-health.*')">Domain health</x-nav-item>
                <x-nav-item href="{{ route('logs.index') }}" icon="list" :active="request()->routeIs('logs.*')">Activity log</x-nav-item>
            </nav>

            <div class="border-t border-gray-100 p-3">
                <x-nav-item href="{{ route('profile.edit') }}" icon="cog" :active="request()->routeIs('profile.*')">Settings</x-nav-item>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white/90 px-4 backdrop-blur sm:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                    <h2 class="text-sm font-semibold text-gray-900 sm:text-base">{{ $title ?? 'Dashboard' }}</h2>
                </div>

                <div class="flex items-center gap-3">
                    @if($user->smtpReady())
                        <span class="badge-green hidden sm:inline-flex"><x-icon name="check" class="h-3 w-3" /> SMTP connected</span>
                    @else
                        <span class="badge-amber hidden sm:inline-flex"><x-icon name="warning" class="h-3 w-3" /> SMTP not set up</span>
                    @endif

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <span class="hidden sm:inline">{{ $user->name }}</span>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false"
                             class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile &amp; settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Log out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <x-flash />
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
