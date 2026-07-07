<x-guest-layout>
    <div class="auth-head">
        <h1>Log in</h1>
        <p>Welcome back. Enter your details to continue.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-ok" role="status" style="margin-top:24px;">
            <x-lucide name="circle-check" />
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert" role="alert" style="margin-top:24px;">
            <x-lucide name="alert-circle" />
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="form" novalidate data-auth-form>
        @csrf

        <div class="field" data-field="email">
            <div class="field-label-row">
                <label for="email">Email</label>
            </div>
            <div class="input-wrap">
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       autocomplete="email" placeholder="you@example.com" class="input" data-validate="email">
            </div>
            <p class="field-error" data-error><x-lucide name="alert-circle" /> <span></span></p>
        </div>

        <div class="field" data-field="password">
            <div class="field-label-row">
                <label for="password">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="field-forgot">Forgot password?</a>
                @endif
            </div>
            <div class="input-wrap">
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       placeholder="Your password" class="input has-toggle" data-validate="required">
                <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Show password">
                    <x-lucide name="eye" class="lucide i-show" />
                    <x-lucide name="eye-off" class="lucide i-hide" />
                </button>
            </div>
            <p class="field-error" data-error><x-lucide name="alert-circle" /> <span></span></p>
        </div>

        <label class="check">
            <input type="checkbox" name="remember">
            <span class="box"><x-lucide name="check" /></span>
            Remember me
        </label>

        <x-recaptcha />

        <button type="submit" class="btn btn-accent btn-block auth-submit" data-submit>
            <span class="label-default">Log in</span>
            <span class="label-loading" hidden><span class="spinner"></span> Authenticating…</span>
        </button>
    </form>
</x-guest-layout>
