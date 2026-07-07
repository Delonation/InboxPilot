<x-layouts.dashboard title="Setup">
    <div class="mx-auto max-w-2xl">
        <x-setup-steps :current="$current" :index="$index" />
        <x-card title="Send a test email" subtitle="We will send a test message to your from-address to confirm everything works.">
            <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-700">
                A test email will be sent to
                <span class="font-medium">{{ $smtp->from_email ?? 'your sender address' }}</span>
                using the SMTP settings you just saved.
            </div>

            @if($smtp && $smtp->last_test_error)
                <div class="mt-4"><x-alert type="error">{{ $smtp->last_test_error }}</x-alert></div>
            @endif

            <form method="POST" action="{{ route('setup.update', 'test') }}" class="mt-6 flex justify-between">
                @csrf
                <a href="{{ route('setup.index', 'smtp') }}" class="btn-secondary">Back to SMTP</a>
                <button type="submit" class="btn-primary">Send test email</button>
            </form>
        </x-card>
    </div>
</x-layouts.dashboard>
