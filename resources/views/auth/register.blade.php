<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Create your account</h1>
        <p class="mt-2 text-sm text-gray-500">
            Free to register. An admin approves new accounts before sending.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5"
          x-data="{ pw: '', show: false, showConfirm: false }">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   autocomplete="name" placeholder="Jane Doe" class="form-input" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   autocomplete="username" placeholder="you@example.com" class="form-input" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password (with show/hide + strength meter) --}}
        <div>
            <label for="password" class="form-label">Password</label>
            <div class="relative">
                <input id="password" name="password" required autocomplete="new-password"
                       placeholder="At least 8 characters"
                       class="form-input pr-11"
                       x-model="pw"
                       :type="show ? 'text' : 'password'" />
                <button type="button" tabindex="-1"
                        @click="show = !show"
                        :aria-label="show ? 'Hide password' : 'Show password'"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition hover:text-gray-700 focus:outline-none">
                    <x-icon name="eye" class="h-5 w-5" x-show="!show" />
                    <x-icon name="eye-slash" class="h-5 w-5" x-show="show" x-cloak />
                </button>
            </div>

            {{-- strength meter --}}
            <div x-cloak x-show="pw.length > 0" class="mt-2"
                 x-data="{
                    get score() {
                        let s = 0;
                        if (this.pw.length >= 8) s++;
                        if (/[A-Z]/.test(this.pw) && /[a-z]/.test(this.pw)) s++;
                        if (/\d/.test(this.pw)) s++;
                        if (/[^A-Za-z0-9]/.test(this.pw)) s++;
                        return s;
                    },
                    get label() { return ['Too weak','Weak','Fair','Good','Strong'][this.score]; },
                    get color() { return ['bg-red-400','bg-red-400','bg-amber-400','bg-blue-500','bg-green-500'][this.score]; }
                 }">
                <div class="flex gap-1.5">
                    <template x-for="i in 4" :key="i">
                        <span class="h-1.5 flex-1 rounded-full transition-colors duration-300"
                              :class="i <= score ? color : 'bg-gray-200'"></span>
                    </template>
                </div>
                <p class="mt-1 text-xs text-gray-500" x-text="label"></p>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm password (with show/hide) --}}
        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <div class="relative">
                <input id="password_confirmation" name="password_confirmation" required
                       autocomplete="new-password" placeholder="Re-enter your password"
                       class="form-input pr-11"
                       :type="showConfirm ? 'text' : 'password'" />
                <button type="button" tabindex="-1"
                        @click="showConfirm = !showConfirm"
                        :aria-label="showConfirm ? 'Hide password' : 'Show password'"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition hover:text-gray-700 focus:outline-none">
                    <x-icon name="eye" class="h-5 w-5" x-show="!showConfirm" />
                    <x-icon name="eye-slash" class="h-5 w-5" x-show="showConfirm" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-recaptcha />

        <button type="submit" class="btn-primary w-full py-2.5 text-sm shadow-sm">
            Create account
        </button>

        <p class="text-center text-sm text-gray-600">
            Already registered?
            <a href="{{ route('login') }}" class="font-medium text-gray-900 hover:underline">Log in</a>
        </p>
    </form>
</x-guest-layout>
