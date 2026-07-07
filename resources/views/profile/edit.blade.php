<x-layouts.dashboard title="Settings">
    <x-page-header title="Profile & settings" subtitle="Manage your account, sender details, and password." />

    <div class="space-y-6">
        <x-card title="Account & sender details">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="name">Name</label>
                        <input id="name" name="name" type="text" class="form-input" value="{{ old('name', $user->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $user->email) }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div>
                        <label class="form-label" for="company_name">Company name</label>
                        <input id="company_name" name="company_name" type="text" class="form-input" value="{{ old('company_name', $profile->company_name) }}" />
                    </div>
                    <div>
                        <label class="form-label" for="timezone">Timezone</label>
                        <select id="timezone" name="timezone" class="form-select">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" @selected(old('timezone', $profile->timezone ?? 'UTC') === $tz)>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="sender_name">Sender name</label>
                        <input id="sender_name" name="sender_name" type="text" class="form-input" value="{{ old('sender_name', $profile->sender_name) }}" />
                    </div>
                    <div>
                        <label class="form-label" for="sender_email">Sender email</label>
                        <input id="sender_email" name="sender_email" type="email" class="form-input" value="{{ old('sender_email', $profile->sender_email) }}" />
                    </div>
                    <div>
                        <label class="form-label" for="reply_to_email">Reply-to email</label>
                        <input id="reply_to_email" name="reply_to_email" type="email" class="form-input" value="{{ old('reply_to_email', $profile->reply_to_email) }}" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Save changes</button>
                </div>
            </form>
        </x-card>

        <x-card title="Update password">
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="form-label" for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" class="form-input" autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-1" />
                    </div>
                    <div>
                        <label class="form-label" for="new_password">New password</label>
                        <input id="new_password" name="password" type="password" class="form-input" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <div>
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Update password</button>
                </div>
            </form>
        </x-card>

        <x-card title="Delete account">
            <p class="text-sm text-gray-500">This permanently deletes your account and all of your data. This cannot be undone.</p>
            <div x-data="{ open: false }" class="mt-4">
                <button @click="open = true" class="btn-danger">Delete account</button>
                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 p-4">
                    <div @click.outside="open = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                        <h3 class="text-base font-semibold text-gray-900">Delete your account?</h3>
                        <p class="mt-1 text-sm text-gray-500">Enter your password to confirm.</p>
                        <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 space-y-3">
                            @csrf
                            @method('DELETE')
                            <input name="password" type="password" class="form-input" placeholder="Password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-danger">Delete account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.dashboard>
