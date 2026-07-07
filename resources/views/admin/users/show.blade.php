<x-layouts.admin title="User details">
    <x-page-header :title="$user->name" :subtitle="$user->email">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Back</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex items-center justify-between">
        <x-status-badge :status="$user->status" />
        @include('admin.users._actions', ['user' => $user])
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <x-stat-card label="Contacts" :value="number_format($stats['contacts'])" />
                <x-stat-card label="Campaigns" :value="number_format($stats['campaigns'])" />
                <x-stat-card label="Sent" :value="number_format($stats['sent'])" tone="success" />
                <x-stat-card label="Failed" :value="number_format($stats['failed'])" tone="danger" />
            </div>

            <x-card title="Account timeline">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Registered</dt><dd class="text-gray-900">{{ $user->created_at->format('M j, Y g:i a') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Approved</dt><dd class="text-gray-900">{{ $user->approved_at?->format('M j, Y') ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Last login</dt><dd class="text-gray-900">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd></div>
                    @if($user->suspension_reason)
                        <div class="flex justify-between"><dt class="text-gray-500">Suspension reason</dt><dd class="text-gray-900">{{ $user->suspension_reason }}</dd></div>
                    @endif
                </dl>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Profile">
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-gray-500">Company</dt><dd class="text-gray-900">{{ $profile->company_name ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Sender email</dt><dd class="text-gray-900">{{ $profile->sender_email ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Timezone</dt><dd class="text-gray-900">{{ $profile->timezone ?? 'UTC' }}</dd></div>
                </dl>
            </x-card>

            <x-card title="SMTP">
                @if($smtpConnected)
                    <span class="badge-green"><x-icon name="check" class="h-3 w-3" /> Connected</span>
                @else
                    <span class="badge-gray">Not connected</span>
                @endif
                <p class="mt-2 text-xs text-gray-400">SMTP passwords are encrypted and never visible to admins.</p>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
