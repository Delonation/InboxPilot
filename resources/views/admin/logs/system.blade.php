<x-layouts.admin title="System logs">
    <x-page-header title="System logs" subtitle="Application-level events and errors." />

    <x-card :pad="false">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>Level</th><th>Message</th><th>Context</th><th>When</th></tr></thead>
                <tbody>
                    @forelse($rows as $log)
                        <tr>
                            <td>
                                @php $c = ['error' => 'red', 'warning' => 'amber', 'info' => 'blue'][$log->level] ?? 'gray'; @endphp
                                <x-badge :color="$c">{{ ucfirst($log->level) }}</x-badge>
                            </td>
                            <td class="font-medium text-gray-900">{{ $log->message }}</td>
                            <td class="max-w-md text-xs text-gray-500">{{ $log->context ? \Illuminate\Support\Str::limit(json_encode($log->context), 80) : '—' }}</td>
                            <td class="text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-sm text-gray-500">No system logs.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 p-4">{{ $rows->links() }}</div>
    </x-card>
</x-layouts.admin>
