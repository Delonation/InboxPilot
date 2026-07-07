<x-layouts.dashboard title="Edit contact">
    <div class="mx-auto max-w-xl">
        <x-page-header title="Edit contact" />
        <x-card>
            <form method="POST" action="{{ route('contacts.update', $contact) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $contact->email) }}" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label" for="first_name">First name</label>
                        <input id="first_name" name="first_name" type="text" class="form-input" value="{{ old('first_name', $contact->first_name) }}" />
                    </div>
                    <div>
                        <label class="form-label" for="last_name">Last name</label>
                        <input id="last_name" name="last_name" type="text" class="form-input" value="{{ old('last_name', $contact->last_name) }}" />
                    </div>
                </div>
                <div>
                    <label class="form-label" for="company">Company</label>
                    <input id="company" name="company" type="text" class="form-input" value="{{ old('company', $contact->company) }}" />
                </div>
                <div>
                    <label class="form-label" for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" class="form-input" value="{{ old('phone', $contact->phone) }}" />
                </div>
                <div>
                    <label class="form-label" for="tags">Tags</label>
                    <input id="tags" name="tags" type="text" class="form-input" value="{{ old('tags', $contact->tags) }}" placeholder="Comma separated" />
                </div>
                <div class="flex justify-between">
                    <a href="{{ route('contacts.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.dashboard>
