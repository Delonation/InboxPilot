<footer class="footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <img src="{{ asset('Logo_inbox_flight.png') }}" alt="" class="brand-logo">
            {{ config('app.name') }}
        </div>
        <p>{{ config('app.name') }} confirms whether your SMTP server accepted each message. It cannot guarantee inbox placement.</p>
    </div>
</footer>

{{-- Product credit — DLN Web Studio --}}
<div class="colophon">
    <div class="container colophon-inner">
        <a href="https://dlnwebstudio.com" target="_blank" rel="noopener noreferrer" class="colophon-link"
           aria-label="Created by DLN Web Studio">
            <span class="colophon-by">Created by</span>
            <img src="{{ asset('logo-white.webp') }}" alt="DLN Web Studio" class="dln-logo">
        </a>
    </div>
</div>
