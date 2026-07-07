<x-guest-layout>
    @if (session('registered'))
        {{-- In-place success state --}}
        <div class="auth-success">
            <span class="big-check"><x-lucide name="circle-check" /></span>
            <h1>Account created</h1>
            <p class="line"><span class="c">250 OK:</span> awaiting admin approval before you can send.</p>
            <a href="{{ route('login') }}" class="btn btn-accent btn-block auth-submit" style="margin-top:26px;">Go to login</a>
        </div>
    @else
        <div class="auth-head">
            <h1>Create your account</h1>
            <p>Free to register. An admin approves new accounts before sending.</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert" style="margin-top:24px;">
                <x-lucide name="alert-circle" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="form" novalidate data-auth-form>
            @csrf

            <div class="field" data-field="name">
                <div class="field-label-row"><label for="name">Name</label></div>
                <div class="input-wrap">
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                           autocomplete="name" placeholder="Jane Doe" class="input" data-validate="required">
                </div>
                <p class="field-error" data-error><x-lucide name="alert-circle" /> <span></span></p>
            </div>

            <div class="field" data-field="email">
                <div class="field-label-row"><label for="email">Email</label></div>
                <div class="input-wrap">
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           autocomplete="email" placeholder="you@example.com" class="input" data-validate="email">
                </div>
                <p class="field-error" data-error><x-lucide name="alert-circle" /> <span></span></p>
            </div>

            <div class="field" data-field="password">
                <div class="field-label-row"><label for="password">Password</label></div>
                <div class="input-wrap">
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           placeholder="At least 8 characters" class="input has-toggle"
                           data-validate="required" data-password>
                    <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Show password">
                        <x-lucide name="eye" class="lucide i-show" />
                        <x-lucide name="eye-off" class="lucide i-hide" />
                    </button>
                </div>
                <div class="pw-meter" data-strength style="display:none;">
                    <div class="pw-meter-bar">
                        <span class="pw-meter-seg"></span>
                        <span class="pw-meter-seg"></span>
                        <span class="pw-meter-seg"></span>
                    </div>
                    <p class="pw-meter-cap" data-cap></p>
                </div>
                <p class="field-error" data-error><x-lucide name="alert-circle" /> <span></span></p>
            </div>

            <div class="field" data-field="password_confirmation">
                <div class="field-label-row"><label for="password_confirmation">Confirm password</label></div>
                <div class="input-wrap">
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           autocomplete="new-password" placeholder="Re-enter your password"
                           class="input has-toggle" data-validate="confirm">
                    <span class="confirm-ok" data-confirm-ok aria-hidden="true"><x-lucide name="circle-check" /></span>
                    <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Show password">
                        <x-lucide name="eye" class="lucide i-show" />
                        <x-lucide name="eye-off" class="lucide i-hide" />
                    </button>
                </div>
                <p class="field-error" data-error><x-lucide name="alert-circle" /> <span></span></p>
            </div>

            <x-recaptcha />

            <button type="submit" class="btn btn-accent btn-block auth-submit" data-submit>
                <span class="label-default">Create account</span>
                <span class="label-loading" hidden><span class="spinner"></span> Creating account…</span>
            </button>
        </form>
    @endif
</x-guest-layout>
