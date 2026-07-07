<x-layouts.dashboard title="Domain health">
    <x-page-header title="Domain health" subtitle="Check the email authentication records for your sending domain." />

    <x-card>
        <form method="POST" action="{{ route('domain-health.check') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            @csrf
            <div class="flex-1">
                <label class="form-label" for="domain">Domain or sender email</label>
                <input id="domain" name="domain" type="text" class="form-input" value="{{ old('domain', $domain) }}" placeholder="example.com" required />
                <x-input-error :messages="$errors->get('domain')" class="mt-1" />
            </div>
            <div class="sm:w-56">
                <label class="form-label" for="selector">DKIM selector <span class="text-gray-400">(optional)</span></label>
                <input id="selector" name="selector" type="text" class="form-input" value="{{ old('selector', $selector) }}" placeholder="e.g. default" />
            </div>
            <button class="btn-primary">Run check</button>
        </form>
    </x-card>

    @if($results)
        @php
            $meta = [
                'mx' => ['MX record', 'Required to receive mail and a basic legitimacy signal.'],
                'spf' => ['SPF record', 'Authorises which servers may send for your domain.'],
                'dmarc' => ['DMARC record', 'Tells receivers how to handle unauthenticated mail.'],
                'dkim' => ['DKIM record', 'Cryptographically signs your mail. Needs a selector to check.'],
            ];
            $badge = ['found' => 'green', 'missing' => 'red', 'warning' => 'amber', 'not_checked' => 'gray'];
            $labelText = ['found' => 'Found', 'missing' => 'Missing', 'warning' => 'Warning', 'not_checked' => 'Not checked'];
        @endphp

        <div class="mt-6 space-y-4">
            @foreach($meta as $key => [$title, $desc])
                @php $r = $results[$key]; @endphp
                <x-card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
                                <x-badge :color="$badge[$r['status']]">{{ $labelText[$r['status']] }}</x-badge>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ $desc }}</p>
                            @if($r['value'])
                                <p class="mt-2 break-all rounded bg-gray-50 px-3 py-2 font-mono text-xs text-gray-700">{{ $r['value'] }}</p>
                            @endif
                            @if($r['fix'])
                                <p class="mt-2 text-xs text-gray-600"><span class="font-medium">Recommendation:</span> {{ $r['fix'] }}</p>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-4">
            <x-alert type="warning">
                Missing authentication records do not stop you from sending, but they can significantly reduce
                deliverability. Adding SPF, DKIM, and DMARC is strongly recommended.
            </x-alert>
        </div>
    @endif
</x-layouts.dashboard>
