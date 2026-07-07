<x-layouts.dashboard title="Activity log">
    <x-page-header title="Activity log" subtitle="Your account activity and SMTP attempts." />

    <div class="mb-4 flex gap-2">
        <a href="{{ route('logs.index', ['tab' => 'activity']) }}" class="{{ $tab === 'activity' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Activity</a>
        <a href="{{ route('logs.index', ['tab' => 'smtp']) }}" class="{{ $tab === 'smtp' ? 'btn-primary' : 'btn-secondary' }} btn-sm">SMTP attempts</a>
    </div>

    <x-card :pad="false">
        <div class="overflow-x-auto">
            <table class="table">
                @if($tab === 'smtp')
                    <thead><tr><th>Result</th><th>Context</th><th>Response / error</th><th>When</th></tr></thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>@if($log->success)<x-badge color="green">Success</x-badge>@else<x-badge color="red">Failed</x-badge>@endif</td>
                                <td class="capitalize">{{ $log->context }}</td>
                                <td class="max-w-md text-xs text-gray-500">{{ $log->error_message ?: ($log->response ?: '—') }}</td>
                                <td class="text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-sm text-gray-500">No SMTP attempts yet.</td></tr>
                        @endforelse
                    </tbody>
                @else
                    <thead><tr><th>Action</th><th>Details</th><th>IP</th><th>When</th></tr></thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="font-medium capitalize text-gray-900">{{ str_replace('_', ' ', $log->action) }}</td>
                                <td class="text-gray-600">{{ $log->details ?: '—' }}</td>
                                <td class="text-gray-500">{{ $log->ip_address ?: '—' }}</td>
                                <td class="text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-sm text-gray-500">No activity yet.</td></tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
        <div class="border-t border-gray-100 p-4">{{ $logs->links() }}</div>
    </x-card>
</x-layouts.dashboard>
