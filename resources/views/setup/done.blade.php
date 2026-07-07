<x-layouts.dashboard title="Setup">
    <div class="mx-auto max-w-2xl">
        <x-setup-steps :current="$current" :index="$index" />
        <x-card>
            <div class="py-6 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
                    <x-icon name="check-circle" class="h-7 w-7" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900">You are all set</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                    Your SMTP connection is verified. Import your contacts, create a template, and send your first campaign.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ route('contacts.import.create') }}" class="btn-secondary">Import contacts</a>
                    <a href="{{ route('campaigns.create') }}" class="btn-primary">Create a campaign</a>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.dashboard>
