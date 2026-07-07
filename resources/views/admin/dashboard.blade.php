<x-layouts.admin title="Overview">
    <x-page-header title="Overview" subtitle="System-wide activity across all users." />

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <x-stat-card label="Total users" :value="number_format($stats['users_total'])" />
        <x-stat-card label="Pending" :value="number_format($stats['users_pending'])" tone="warning" />
        <x-stat-card label="Approved" :value="number_format($stats['users_approved'])" tone="success" />
        <x-stat-card label="Rejected" :value="number_format($stats['users_rejected'])" tone="danger" />
        <x-stat-card label="Suspended" :value="number_format($stats['users_suspended'])" tone="danger" />
    </div>

    <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Campaigns" :value="number_format($stats['campaigns'])" />
        <x-stat-card label="Emails attempted" :value="number_format($stats['attempted'])" />
        <x-stat-card label="Sent" :value="number_format($stats['sent'])" tone="success" />
        <x-stat-card label="Failed" :value="number_format($stats['failed'])" tone="danger" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <x-card title="Recent registrations">
            @forelse($recentRegistrations as $u)
                <div class="flex items-center justify-between border-b border-gray-100 py-2 last:border-0">
                    <div>
                        <a href="{{ route('admin.users.show', $u) }}" class="text-sm font-medium text-gray-900 hover:underline">{{ $u->name }}</a>
                        <p class="text-xs text-gray-500">{{ $u->email }}</p>
                    </div>
                    <x-status-badge :status="$u->status" />
                </div>
            @empty
                <p class="py-4 text-center text-sm text-gray-500">No registrations.</p>
            @endforelse
        </x-card>

        <x-card title="Recent campaign errors">
            @forelse($recentCampaignErrors as $r)
                <div class="border-b border-gray-100 py-2 text-xs last:border-0">
                    <p class="font-medium text-gray-700">{{ $r->email }}</p>
                    <p class="text-gray-500">{{ $r->campaign?->user?->email }} · {{ \Illuminate\Support\Str::limit($r->error_message, 50) }}</p>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-gray-500">No campaign errors.</p>
            @endforelse
        </x-card>

        <x-card title="Recent SMTP errors">
            @forelse($recentSmtpErrors as $log)
                <div class="border-b border-gray-100 py-2 text-xs last:border-0">
                    <p class="font-medium text-gray-700">{{ $log->user?->email }}</p>
                    <p class="text-gray-500">{{ \Illuminate\Support\Str::limit($log->error_message, 60) }}</p>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-gray-500">No SMTP errors.</p>
            @endforelse
        </x-card>
    </div>
</x-layouts.admin>
