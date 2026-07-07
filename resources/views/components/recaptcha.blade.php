@php $recaptcha = app(\App\Services\RecaptchaService::class); @endphp

@if ($recaptcha->enabled())
    <div>
        <div class="g-recaptcha" data-sitekey="{{ $recaptcha->siteKey() }}"></div>
        <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
    </div>
    @once
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endpush
    @endonce
@endif
