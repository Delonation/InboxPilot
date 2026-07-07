@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}Admin · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans">
    @php($user = auth()->user())
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-gray-200 bg-gray-900 transition-transform lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-gray-900">
                    <x-icon name="shield" class="h-4 w-4" />
                </span>
                <span class="text-base font-semibold text-white">{{ config('app.name') }} Admin</span>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-gray-300">
                @foreach([
                    ['admin.dashboard', 'home', 'Overview', 'admin.dashboard'],
                    ['admin.users.pending', 'clock', 'Pending users', 'admin.users.pending'],
                    ['admin.users.index', 'users', 'All users', 'admin.users.index'],
                    ['admin.logs.campaigns', 'send', 'Campaign logs', 'admin.logs.campaigns'],
                    ['admin.logs.smtp', 'server', 'SMTP logs', 'admin.logs.smtp'],
                    ['admin.logs.system', 'list', 'System logs', 'admin.logs.system'],
                    ['admin.settings.edit', 'cog', 'Settings', 'admin.settings.*'],
                ] as [$route, $icon, $label, $pattern])
                    <a href="{{ route($route) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                              {{ request()->routeIs($pattern) ? 'bg-white text-gray-900' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white">
                    <x-icon name="chevron-right" class="h-5 w-5" /> Back to app
                </a>
            </div>
        </aside>

        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                    <h2 class="text-sm font-semibold text-gray-900 sm:text-base">{{ $title ?? 'Admin' }}</h2>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline">{{ $user->name }}</span>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-2 w-44 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Log out</button>
                        </form>
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
