<x-layouts.dashboard title="Setup">
    <div class="mx-auto max-w-2xl">
        <x-setup-steps :current="$current" :index="$index" />
        <x-card title="Connect your SMTP" subtitle="InboxPilot sends through your own SMTP account.">
            <form method="POST" action="{{ route('setup.update', 'smtp') }}">
                @csrf
                @include('smtp._form', ['smtp' => $smtp])
                <div class="mt-6 flex justify-between">
                    <a href="{{ route('setup.index', 'sender') }}" class="btn-secondary">Back</a>
                    <button type="submit" class="btn-primary">Save and continue</button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.dashboard>
