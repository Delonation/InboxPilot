<x-layouts.dashboard title="Recipients">
    <x-page-header :title="$campaign->name.' · recipients'" subtitle="Per-recipient delivery results.">
        <x-slot:actions>
            <a href="{{ route('campaigns.show', $campaign) }}" class="btn-secondary">Back to report</a>
        </x-slot:actions>
    </x-page-header>

    <x-card :pad="false">
        <form method="GET" class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1 max-w-sm">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400"><x-icon name="search" class="h-4 w-4" /></span>
                <input name="q" value="{{ $search }}" placeholder="Search email" class="form-input pl-9" />
            </div>
            <select name="status" class="form-select sm:w-56" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach(['sent','failed','skipped_unsubscribed','skipped_invalid','pending'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr><th>Email</th><th>Name</th><th>Status</th><th>SMTP response / error</th><th>Sent at</th></tr>
                </thead>
                <tbody>
                    @forelse($recipients as $r)
                        <tr>
                            <td class="font-medium text-gray-900">{{ $r->email }}</td>
                            <td>{{ $r->name ?: '—' }}</td>
                            <td><x-status-badge :status="$r->status" /></td>
                            <td class="max-w-md text-xs text-gray-500">{{ $r->error_message ?: ($r->smtp_response ?: '—') }}</td>
                            <td class="text-gray-500">{{ $r->sent_at?->format('M j, g:i a') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-sm text-gray-500">No recipients match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 p-4">{{ $recipients->links() }}</div>
    </x-card>
</x-layouts.dashboard>
