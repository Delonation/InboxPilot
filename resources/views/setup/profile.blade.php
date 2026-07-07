<x-layouts.dashboard title="Setup">
    <div class="mx-auto max-w-2xl">
        <x-setup-steps :current="$current" :index="$index" />
        <x-card title="Profile details" subtitle="Tell us who you are. You can change this later in settings.">
            <form method="POST" action="{{ route('setup.update', 'profile') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label" for="name">Your name</label>
                    <input id="name" name="name" type="text" class="form-input" value="{{ old('name', $user->name) }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <label class="form-label" for="company_name">Company name <span class="text-gray-400">(optional)</span></label>
                    <input id="company_name" name="company_name" type="text" class="form-input" value="{{ old('company_name', $profile->company_name) }}" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Continue</button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.dashboard>
