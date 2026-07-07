<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Log in to {{ config('app.name') }}</h1>
        <p class="mt-2 text-sm text-gray-500">Welcome back. Enter your details to continue.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5"
          x-data="{ show: false }">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username" placeholder="you@example.com" class="form-input" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password (with show/hide) --}}
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="form-label">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="mb-1 text-xs font-medium text-gray-500 hover:text-gray-900">Forgot password?</a>
                @endif
            </div>
            <div class="relative">
                <input id="password" name="password" required autocomplete="current-password"
                       placeholder="Your password"
                       class="form-input pr-11"
                       :type="show ? 'text' : 'password'" />
                <button type="button" tabindex="-1"
                        @click="show = !show"
                        :aria-label="show ? 'Hide password' : 'Show password'"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition hover:text-gray-700 focus:outline-none">
                    <x-icon name="eye" class="h-5 w-5" x-show="!show" />
                    <x-icon name="eye-slash" class="h-5 w-5" x-show="show" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember me --}}
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" name="remember"
                   class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-900">
            <span class="ms-2 text-sm text-gray-600">Remember me</span>
        </label>

        <x-recaptcha />

        <button type="submit" class="btn-primary w-full py-2.5 text-sm shadow-sm">Log in</button>

        @if(config('inboxpilot.registration_open'))
            <p class="text-center text-sm text-gray-600">
                Need an account?
                <a href="{{ route('register') }}" class="font-medium text-gray-900 hover:underline">Register</a>
            </p>
        @endif
    </form>
</x-guest-layout>
