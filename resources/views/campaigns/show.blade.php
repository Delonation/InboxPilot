<x-layouts.dashboard title="Campaign report">
    <x-page-header :title="$campaign->name" :subtitle="'Sent '.($campaign->completed_at?->format('M j, Y g:i a') ?? '—')">
        <x-slot:actions>
            <a href="{{ route('campaigns.recipients', $campaign) }}" class="btn-secondary">Recipient details</a>
            @if($campaign->isSending())
                <a href="{{ route('campaigns.send', $campaign) }}" class="btn-primary">Resume sending</a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4"><x-status-badge :status="$campaign->status" /></div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Recipients" :value="number_format($campaign->total_recipients)" />
        <x-stat-card label="Sent" :value="number_format($campaign->total_sent)" tone="success" />
        <x-stat-card label="Failed" :value="number_format($campaign->total_failed)" tone="danger" />
        <x-stat-card label="Skipped" :value="number_format($campaign->total_skipped)" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card title="Delivery breakdown">
                <div class="space-y-3">
                    @php
                        $rows = [
                            'sent' => ['Sent', 'green'],
                            'failed' => ['Failed', 'red'],
                            'skipped_unsubscribed' => ['Skipped (unsubscribed)', 'gray'],
                            'skipped_invalid' => ['Skipped (invalid)', 'gray'],
                            'pending' => ['Pending', 'amber'],
                        ];
                    @endphp
                    @foreach($rows as $key => [$label, $color])
                        <div class="flex items-center justify-between">
                            <x-badge :color="$color">{{ $label }}</x-badge>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($breakdown[$key] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <div class="mt-4">
                <x-alert type="info" title="What this report means">
                    InboxPilot confirms whether your SMTP server accepted each message. It cannot confirm inbox
                    placement, opens, or clicks. Inbox delivery depends on your domain authentication (SPF, DKIM,
                    DMARC) and your SMTP provider's reputation.
                </x-alert>
            </div>
        </div>

        <x-card title="Details">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">Template</dt><dd class="font-medium text-gray-900">{{ $campaign->template->name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Subject</dt><dd class="font-medium text-gray-900">{{ $campaign->effectiveSubject() }}</dd></div>
                <div><dt class="text-gray-500">Sender</dt><dd class="font-medium text-gray-900">{{ $campaign->sender_email }}</dd></div>
                <div><dt class="text-gray-500">SMTP account</dt><dd class="font-medium text-gray-900">{{ $campaign->smtp_summary }}</dd></div>
                <div><dt class="text-gray-500">Started</dt><dd class="font-medium text-gray-900">{{ $campaign->started_at?->format('M j, Y g:i a') ?? '—' }}</dd></div>
            </dl>
        </x-card>
    </div>
</x-layouts.dashboard>
