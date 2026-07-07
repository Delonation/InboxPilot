<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} · {{ request()->routeIs('register') ? 'Create account' : 'Log in' }}</title>
    @include('partials.site-head')
</head>
<body>

@php $ctx = request()->routeIs('register') ? 'register' : 'login'; @endphp

<x-site-nav variant="auth" :context="$ctx" />

<main>
    <div class="auth-split">
        <div class="auth-form-col">
            <div class="auth-form">
                {{ $slot }}
            </div>
        </div>

        {{-- Dark brand panel: ambient SMTP transcript (decorative) --}}
        <aside class="auth-panel" aria-hidden="true">
            <span class="pc pc1">220</span>
            <span class="pc pc2">250</span>
            <span class="pc pc3">221</span>
            <div class="auth-panel-inner">
                <div class="auth-tr-mask">
                    <div class="auth-tr-track" data-tr-track>
                        @php
                            $tr = [
                                '<span class="cr">220</span> smtp.hostinger.com ESMTP ready',
                                'EHLO inboxpilot.local',
                                '<span class="cr">250</span>-STARTTLS',
                                '<span class="cr">250</span> AUTH LOGIN PLAIN',
                                '→ TLS established (TLS 1.3)',
                                'AUTH LOGIN  <span class="cr">334</span> dXNlcm5hbWU6',
                                '<span class="cr">235</span> authenticated',
                                'MAIL FROM:&lt;hello@…&gt;  <span class="cr">250</span> OK',
                                'RCPT TO:&lt;ava@acme.io&gt;  <span class="cr">250</span> Accepted',
                                'DATA  <span class="cr">354</span> Go ahead',
                                '<span class="cr">250</span> OK: queued as 4Xz91k',
                                '<span class="cr">221</span> Bye',
                            ];
                        @endphp
                        {{-- rendered twice for a seamless loop --}}
                        @foreach ($tr as $line)<div class="ln">{!! $line !!}</div>@endforeach
                        @foreach ($tr as $line)<div class="ln">{!! $line !!}</div>@endforeach
                    </div>
                </div>
            </div>
            <p class="auth-panel-note"><span class="c">250 OK:</span> your credentials are encrypted at rest.</p>
        </aside>
    </div>
</main>

<x-site-footer />

@stack('scripts')

<script>
    // Ambient transcript loop in the dark auth panel
    (function () {
        const track = document.querySelector('[data-tr-track]');
        if (!track) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; // static
        const lines = track.querySelectorAll('.ln');
        const total = lines.length / 2;
        const lh = lines[0] ? lines[0].getBoundingClientRect().height : 27;
        let n = 0;
        setTimeout(() => {
            setInterval(() => {
                n++;
                track.style.transform = 'translateY(-' + (n * lh) + 'px)';
                if (n >= total) {
                    setTimeout(() => {
                        track.style.transition = 'none';
                        n = 0;
                        track.style.transform = 'translateY(0)';
                        void track.offsetHeight; // reflow
                        track.style.transition = '';
                    }, 620);
                }
            }, 2500);
        }, 450);
    })();

    // Auth forms: password toggles, blur validation, strength meter, submit state
    (function () {
        const forms = document.querySelectorAll('[data-auth-form]');
        if (!forms.length) return;
        const reduce = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const fieldOf = (input) => input.closest('.field');

        function showError(field, msg) {
            field.classList.add('has-error');
            const err = field.querySelector('[data-error]');
            if (err) { err.querySelector('span').textContent = msg; err.classList.add('show'); }
        }
        function clearError(field) {
            field.classList.remove('has-error');
            const err = field.querySelector('[data-error]');
            if (err) err.classList.remove('show');
        }
        function shake(field) {
            if (reduce()) return;
            field.classList.remove('shake'); void field.offsetWidth; field.classList.add('shake');
        }
        function validate(input) {
            const type = input.getAttribute('data-validate');
            if (!type) return true;
            const field = fieldOf(input);
            const val = input.value.trim();
            if (val === '') {
                if (input.hasAttribute('required')) { showError(field, 'This field is required.'); return false; }
                clearError(field); return true;
            }
            if (type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                showError(field, 'Enter a valid email address.'); return false;
            }
            if (type === 'confirm') {
                const pw = input.form.querySelector('[data-password]');
                if (pw && val !== pw.value) { showError(field, 'Passwords do not match.'); return false; }
            }
            clearError(field); return true;
        }

        forms.forEach((form) => {
            form.querySelectorAll('[data-pw-toggle]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const input = btn.parentElement.querySelector('input');
                    const reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';
                    btn.setAttribute('data-visible', reveal ? 'true' : 'false');
                    btn.setAttribute('aria-pressed', reveal ? 'true' : 'false');
                    btn.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
                });
            });

            form.querySelectorAll('input[data-validate]').forEach((input) => {
                input.addEventListener('blur', () => validate(input));
                input.addEventListener('input', () => { if (fieldOf(input).classList.contains('has-error')) validate(input); });
            });

            const pw = form.querySelector('[data-password]');
            const meter = form.querySelector('[data-strength]');
            if (pw && meter) {
                pw.addEventListener('input', () => {
                    const v = pw.value;
                    let variety = 0;
                    if (/[a-z]/.test(v) && /[A-Z]/.test(v)) variety++;
                    if (/\d/.test(v)) variety++;
                    if (/[^A-Za-z0-9]/.test(v)) variety++;
                    let score = 0;
                    if (v.length >= 10 && variety >= 2) score = 3;
                    else if (v.length >= 8 && variety >= 1) score = 2;
                    else if (v.length > 0) score = 1;
                    meter.style.display = v.length ? '' : 'none';
                    meter.setAttribute('data-score', String(score));
                    const cap = meter.querySelector('[data-cap]');
                    if (cap) cap.textContent = ['', 'weak', 'okay', 'strong'][score] || '';
                });
            }

            const confirm = form.querySelector('input[data-validate="confirm"]');
            if (confirm && pw) {
                const okIcon = form.querySelector('[data-confirm-ok]');
                const sync = () => { if (okIcon) okIcon.classList.toggle('show', confirm.value.length > 0 && confirm.value === pw.value); };
                confirm.addEventListener('input', sync);
                pw.addEventListener('input', sync);
            }

            form.addEventListener('submit', (e) => {
                let firstInvalid = null;
                form.querySelectorAll('input[data-validate]').forEach((input) => {
                    if (!validate(input) && !firstInvalid) firstInvalid = input;
                });
                if (firstInvalid) { e.preventDefault(); shake(fieldOf(firstInvalid)); firstInvalid.focus(); return; }
                const btn = form.querySelector('[data-submit]');
                if (btn) setTimeout(() => {
                    btn.disabled = true;
                    const d = btn.querySelector('.label-default'), l = btn.querySelector('.label-loading');
                    if (d) d.hidden = true; if (l) l.hidden = false;
                }, 0);
            });
        });
    })();
</script>
</body>
</html>
