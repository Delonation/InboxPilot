<x-layouts.dashboard title="Setup">
    <div class="mx-auto max-w-2xl">
        <x-setup-steps :current="$current" :index="$index" />
        <x-card title="Sender details" subtitle="These appear in the emails your contacts receive.">
            <form method="POST" action="{{ route('setup.update', 'sender') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label" for="sender_name">Sender name</label>
                    <input id="sender_name" name="sender_name" type="text" class="form-input" value="{{ old('sender_name', $profile->sender_name ?? $user->name) }}" required />
                    <x-input-error :messages="$errors->get('sender_name')" class="mt-1" />
                </div>
                <div>
                    <label class="form-label" for="sender_email">Sender email</label>
                    <input id="sender_email" name="sender_email" type="email" class="form-input" value="{{ old('sender_email', $profile->sender_email) }}" required />
                    <p class="form-hint">Usually the same address as your SMTP from-address.</p>
                    <x-input-error :messages="$errors->get('sender_email')" class="mt-1" />
                </div>
                <div>
                    <label class="form-label" for="reply_to_email">Reply-to email <span class="text-gray-400">(optional)</span></label>
                    <input id="reply_to_email" name="reply_to_email" type="email" class="form-input" value="{{ old('reply_to_email', $profile->reply_to_email) }}" />
                    <x-input-error :messages="$errors->get('reply_to_email')" class="mt-1" />
                </div>
                <div>
                    <label class="form-label" for="timezone">Timezone</label>
                    <select id="timezone" name="timezone" class="form-select">
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" @selected(old('timezone', $profile->timezone ?? 'UTC') === $tz)>{{ $tz }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('timezone')" class="mt-1" />
                </div>
                <div class="flex justify-between">
                    <a href="{{ route('setup.index', 'profile') }}" class="btn-secondary">Back</a>
                    <button type="submit" class="btn-primary">Continue</button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.dashboard>
