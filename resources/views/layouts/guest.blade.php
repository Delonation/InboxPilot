<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10">
        {{-- Boxed container — the website stays contained, never full-bleed --}}
        <div class="w-full max-w-4xl">
            <div class="card overflow-hidden lg:grid lg:grid-cols-2">

                {{-- Left: brand panel (desktop only) --}}
                <div class="relative hidden overflow-hidden bg-gray-900 p-10 lg:flex lg:flex-col lg:justify-between">
                    <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/5 blur-3xl"></div>
                    <div class="pointer-events-none absolute bottom-0 right-0 h-80 w-80 translate-x-1/3 translate-y-1/3 rounded-full bg-indigo-500/10 blur-3xl"></div>

                    <a href="{{ url('/') }}" class="relative flex items-center gap-2.5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-gray-900">
                            <x-icon name="send" class="h-5 w-5" />
                        </span>
                        <span class="text-lg font-semibold text-white">{{ config('app.name') }}</span>
                    </a>

                    <div class="relative">
                        <h2 class="text-2xl font-semibold leading-tight text-white">
                            Land in the inbox,<br>not the spam folder.
                        </h2>
                        <p class="mt-3 text-sm leading-relaxed text-gray-400">
                            Send campaigns through your own encrypted SMTP and watch your
                            deliverability with built-in domain health checks.
                        </p>

                        <ul class="mt-8 space-y-4">
                            @foreach ([
                                ['shield', 'Encrypted per-user SMTP'],
                                ['chart', 'SPF, DKIM &amp; DMARC checks'],
                                ['users', 'Clean, managed contact lists'],
                            ] as [$icon, $title])
                                <li class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white">
                                        <x-icon :name="$icon" class="h-4 w-4" />
                                    </span>
                                    <span class="text-sm font-medium text-white">{!! $title !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="relative text-xs text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                </div>

                {{-- Right: page content --}}
                <div class="bg-white p-8 sm:p-10">
                    {{-- compact logo for mobile (brand panel is hidden there) --}}
                    <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2 lg:hidden">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-900 text-white">
                            <x-icon name="send" class="h-5 w-5" />
                        </span>
                        <span class="text-lg font-semibold text-gray-900">{{ config('app.name') }}</span>
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
