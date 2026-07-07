<x-layouts.dashboard title="SMTP settings">
    <x-page-header title="SMTP settings" subtitle="InboxPilot sends every campaign through your own SMTP account." />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card title="Connection">
                <form method="POST" action="{{ route('smtp.update') }}">
                    @csrf
                    @method('PUT')
                    @include('smtp._form', ['smtp' => $smtp])
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary">Save settings</button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Status">
                @if($smtp && $smtp->last_test_passed_at)
                    <span class="badge-green"><x-icon name="check" class="h-3 w-3" /> Test passed</span>
                    <p class="mt-2 text-xs text-gray-500">Last passed {{ $smtp->last_test_passed_at->diffForHumans() }}.</p>
                @elseif($smtp)
                    <span class="badge-amber"><x-icon name="warning" class="h-3 w-3" /> Not tested yet</span>
                    @if($smtp->last_test_error)
                        <p class="mt-2 text-xs text-red-600">{{ $smtp->last_test_error }}</p>
                    @endif
                @else
                    <span class="badge-gray">No SMTP configured</span>
                @endif

                @if($smtp)
                    <form method="POST" action="{{ route('smtp.test') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-secondary w-full">Send test email</button>
                    </form>
                @endif
            </x-card>

            <x-card title="Which SMTP works?">
                <ul class="space-y-2 text-xs text-gray-600">
                    <li><span class="font-medium text-gray-900">Hostinger mailbox</span> · smtp.hostinger.com · 465 SSL · best on Hostinger.</li>
                    <li><span class="font-medium text-gray-900">Gmail</span> · smtp.gmail.com · 587 TLS · needs an app password.</li>
                    <li><span class="font-medium text-gray-900">Outlook / 365</span> · smtp.office365.com · 587 TLS.</li>
                    <li><span class="font-medium text-gray-900">Brevo / SendGrid / Mailgun / SES</span> · 587 TLS.</li>
                    <li class="text-red-600">Port 25 is blocked on shared hosting. Do not use it.</li>
                </ul>
            </x-card>
        </div>
    </div>
</x-layouts.dashboard>
