<x-layouts.dashboard title="Campaigns">
    <x-page-header title="Campaigns" subtitle="Send and review your email campaigns.">
        <x-slot:actions>
            <a href="{{ route('campaigns.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> New campaign</a>
        </x-slot:actions>
    </x-page-header>

    <x-card :pad="false">
        @if($campaigns->count())
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Campaign</th><th>Template</th><th>Recipients</th><th>Sent</th><th>Failed</th><th>Status</th><th>Date</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                            <tr>
                                <td class="font-medium text-gray-900">{{ $campaign->name }}</td>
                                <td>{{ $campaign->template->name ?? '—' }}</td>
                                <td>{{ number_format($campaign->total_recipients) }}</td>
                                <td class="text-green-600">{{ number_format($campaign->total_sent) }}</td>
                                <td class="text-red-600">{{ number_format($campaign->total_failed) }}</td>
                                <td><x-status-badge :status="$campaign->status" /></td>
                                <td class="text-gray-500">{{ $campaign->created_at->format('M j, Y') }}</td>
                                <td class="text-right">
                                    @if($campaign->isSending())
                                        <a href="{{ route('campaigns.send', $campaign) }}" class="btn-secondary btn-sm">Resume</a>
                                    @else
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="btn-ghost btn-sm">Report</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 p-4">{{ $campaigns->links() }}</div>
        @else
            <x-empty-state icon="send" title="No campaigns yet" message="Create a campaign to send a template to your contacts.">
                <x-slot:action><a href="{{ route('campaigns.create') }}" class="btn-primary">New campaign</a></x-slot:action>
            </x-empty-state>
        @endif
    </x-card>
</x-layouts.dashboard>
