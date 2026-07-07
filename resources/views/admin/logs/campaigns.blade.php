<x-layouts.admin title="Campaign logs">
    <x-page-header title="Campaign logs" subtitle="Failed recipient deliveries across all users." />

    <x-card :pad="false">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>User</th><th>Campaign</th><th>Recipient</th><th>Error</th><th>When</th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td class="text-gray-600">{{ $r->campaign?->user?->email ?? '—' }}</td>
                            <td class="font-medium text-gray-900">{{ $r->campaign?->name ?? '—' }}</td>
                            <td>{{ $r->email }}</td>
                            <td class="max-w-md text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($r->error_message, 80) }}</td>
                            <td class="text-gray-500">{{ $r->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-sm text-gray-500">No campaign errors.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 p-4">{{ $rows->links() }}</div>
    </x-card>
</x-layouts.admin>
