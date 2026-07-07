{{-- Shared SMTP fields. Expects: $smtp (nullable SmtpSetting). --}}
<div class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="form-label" for="host">SMTP host</label>
            <input id="host" name="host" type="text" class="form-input" placeholder="smtp.hostinger.com" value="{{ old('host', $smtp->host ?? '') }}" required />
            <x-input-error :messages="$errors->get('host')" class="mt-1" />
        </div>
        <div>
            <label class="form-label" for="port">Port</label>
            <input id="port" name="port" type="number" class="form-input" placeholder="465" value="{{ old('port', $smtp->port ?? 465) }}" required />
            <p class="form-hint">Use 465 (SSL) or 587 (TLS). Never 25.</p>
            <x-input-error :messages="$errors->get('port')" class="mt-1" />
        </div>
        <div>
            <label class="form-label" for="encryption">Encryption</label>
            <select id="encryption" name="encryption" class="form-select">
                @foreach(['ssl' => 'SSL (port 465)', 'tls' => 'TLS / STARTTLS (port 587)', 'none' => 'None'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('encryption', $smtp->encryption ?? 'ssl') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('encryption')" class="mt-1" />
        </div>
        <div>
            <label class="form-label" for="username">SMTP username</label>
            <input id="username" name="username" type="text" class="form-input" autocomplete="off" value="{{ old('username', $smtp->username ?? '') }}" required />
            <x-input-error :messages="$errors->get('username')" class="mt-1" />
        </div>
        <div>
            <label class="form-label" for="password">SMTP password</label>
            <input id="password" name="password" type="password" class="form-input" autocomplete="new-password"
                   placeholder="{{ $smtp ? 'Leave blank to keep current' : '' }}" @if(!$smtp) required @endif />
            <p class="form-hint">
                @if($smtp) Stored encrypted. Leave blank to keep the existing password. @else Stored encrypted and never shown again. @endif
            </p>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>
        <div>
            <label class="form-label" for="from_name">From name</label>
            <input id="from_name" name="from_name" type="text" class="form-input" value="{{ old('from_name', $smtp->from_name ?? auth()->user()->name) }}" required />
            <x-input-error :messages="$errors->get('from_name')" class="mt-1" />
        </div>
        <div>
            <label class="form-label" for="from_email">From email</label>
            <input id="from_email" name="from_email" type="email" class="form-input" value="{{ old('from_email', $smtp->from_email ?? '') }}" required />
            <x-input-error :messages="$errors->get('from_email')" class="mt-1" />
        </div>
        <div class="sm:col-span-2">
            <label class="form-label" for="reply_to_email">Reply-to email <span class="text-gray-400">(optional)</span></label>
            <input id="reply_to_email" name="reply_to_email" type="email" class="form-input" value="{{ old('reply_to_email', $smtp->reply_to_email ?? '') }}" />
            <x-input-error :messages="$errors->get('reply_to_email')" class="mt-1" />
        </div>
    </div>

    <x-alert type="info">
        On shared hosting, outbound port 25 is blocked. Use 465 (SSL) or 587 (TLS). A mailbox on your own
        domain (for example smtp.hostinger.com) is the most reliable choice. Gmail and Outlook work with an
        app password.
    </x-alert>
</div>
