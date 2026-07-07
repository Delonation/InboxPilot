<x-layouts.admin title="SMTP logs">
    <x-page-header title="SMTP logs" subtitle="SMTP test and send attempts across all users." />

    <x-card :pad="false">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>User</th><th>Result</th><th>Context</th><th>Response / error</th><th>When</th></tr></thead>
                <tbody>
                    @forelse($rows as $log)
                        <tr>
                            <td class="text-gray-600">{{ $log->user?->email ?? '—' }}</td>
                            <td>@if($log->success)<x-badge color="green">Success</x-badge>@else<x-badge color="red">Failed</x-badge>@endif</td>
                            <td class="capitalize">{{ $log->context }}</td>
                            <td class="max-w-md text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($log->error_message ?: $log->response, 80) }}</td>
                            <td class="text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-sm text-gray-500">No SMTP logs.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 p-4">{{ $rows->links() }}</div>
    </x-card>
</x-layouts.admin>
