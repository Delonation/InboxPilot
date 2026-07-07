<x-layouts.dashboard title="Dashboard">
    @unless($setup['complete'])
        <div class="mb-6">
            <x-card>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Finish setting up your account</h3>
                        <p class="mt-1 text-sm text-gray-500">Complete these steps before you can send campaigns.</p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="{{ $setup['profile'] ? 'badge-green' : 'badge-gray' }}">1. Profile</span>
                            <span class="{{ $setup['smtp'] ? 'badge-green' : 'badge-gray' }}">2. SMTP</span>
                            <span class="{{ $setup['tested'] ? 'badge-green' : 'badge-gray' }}">3. Test email</span>
                        </div>
                    </div>
                    <a href="{{ route('setup.index') }}" class="btn-primary shrink-0">Continue setup</a>
                </div>
            </x-card>
        </div>
    @endunless

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <x-stat-card label="Contacts" :value="number_format($stats['contacts'])" />
        <x-stat-card label="Templates" :value="number_format($stats['templates'])" />
        <x-stat-card label="Campaigns sent" :value="number_format($stats['campaigns_sent'])" />
        <x-stat-card label="Emails attempted" :value="number_format($stats['attempted'])" />
        <x-stat-card label="Sent" :value="number_format($stats['sent'])" tone="success" />
        <x-stat-card label="Failed" :value="number_format($stats['failed'])" tone="danger" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-card title="Recent campaigns">
            <x-slot:actions>
                <a href="{{ route('campaigns.index') }}" class="btn-ghost btn-sm">View all</a>
            </x-slot:actions>
            @forelse($recentCampaigns as $campaign)
                <div class="flex items-center justify-between border-b border-gray-100 py-3 last:border-0">
                    <div>
                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-sm font-medium text-gray-900 hover:underline">{{ $campaign->name }}</a>
                        <p class="text-xs text-gray-500">{{ $campaign->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500">{{ $campaign->total_sent }} sent / {{ $campaign->total_failed }} failed</span>
                        <x-status-badge :status="$campaign->status" />
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500">No campaigns yet.</p>
            @endforelse
        </x-card>

        <x-card title="SMTP connection">
            @if($user->smtpReady())
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 text-green-600"><x-icon name="check-circle" class="h-5 w-5" /></span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Connected</p>
                        <p class="text-xs text-gray-500">{{ $user->smtpSetting->summary() }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-amber-600"><x-icon name="warning" class="h-5 w-5" /></span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Not connected</p>
                        <p class="text-xs text-gray-500">Connect your SMTP account to start sending.</p>
                    </div>
                </div>
                <a href="{{ route('smtp.edit') }}" class="btn-secondary btn-sm mt-4">Set up SMTP</a>
            @endif

            <h4 class="mt-6 mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Recent sending errors</h4>
            @forelse($recentErrors as $err)
                <div class="border-b border-gray-100 py-2 text-xs last:border-0">
                    <p class="font-medium text-gray-700">{{ $err->email }}</p>
                    <p class="text-gray-500">{{ \Illuminate\Support\Str::limit($err->error_message, 80) }}</p>
                </div>
            @empty
                <p class="py-3 text-xs text-gray-400">No sending errors.</p>
            @endforelse
        </x-card>
    </div>
</x-layouts.dashboard>
