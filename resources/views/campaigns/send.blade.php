<x-layouts.dashboard title="Sending campaign">
    <div class="mx-auto max-w-2xl"
         x-data="campaignSender({
            batchUrl: '{{ route('campaigns.batch', $campaign) }}',
            reportUrl: '{{ route('campaigns.show', $campaign) }}',
            recipients: {{ $campaign->total_recipients }},
            initial: {
                sent: {{ $campaign->total_sent }},
                failed: {{ $campaign->total_failed }},
                skipped: {{ $campaign->total_skipped }},
                status: '{{ $campaign->status }}'
            }
         })">
        <x-page-header title="Sending campaign" :subtitle="$campaign->name" />

        <x-card>
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-medium text-gray-900" x-text="done ? 'Sending complete' : (paused ? 'Paused' : 'Sending...')"></span>
                <span class="text-gray-500"><span x-text="processed"></span> / <span x-text="recipients"></span></span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                <div class="h-2 rounded-full bg-gray-900 transition-all" :style="`width: ${pct}%`"></div>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-3">
                <div class="rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-xl font-semibold text-green-600" x-text="totals.sent"></p>
                    <p class="text-xs text-gray-500">Sent</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-xl font-semibold text-red-600" x-text="totals.failed"></p>
                    <p class="text-xs text-gray-500">Failed</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-xl font-semibold text-gray-700" x-text="totals.skipped"></p>
                    <p class="text-xs text-gray-500">Skipped</p>
                </div>
            </div>

            <div x-show="!done" x-cloak class="mt-4">
                <x-alert type="warning">Keep this tab open while sending. If you close it the campaign pauses and you can resume it later from the campaigns list.</x-alert>
            </div>
            <div x-show="error" x-cloak class="mt-4"><x-alert type="error" x-text="error"></x-alert></div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" x-show="!done && !paused" @click="paused = true" class="btn-secondary">Pause</button>
                <button type="button" x-show="!done && paused" @click="resume()" class="btn-primary">Resume</button>
                <a :href="reportUrl" x-show="done" x-cloak class="btn-primary">View report</a>
            </div>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function campaignSender(config) {
            return {
                batchUrl: config.batchUrl,
                reportUrl: config.reportUrl,
                recipients: config.recipients,
                totals: { sent: config.initial.sent, failed: config.initial.failed, skipped: config.initial.skipped },
                done: ['completed', 'completed_with_errors', 'failed'].includes(config.initial.status),
                paused: false,
                error: null,
                get processed() { return this.totals.sent + this.totals.failed + this.totals.skipped; },
                get pct() { return this.recipients ? Math.min(100, Math.round((this.processed / this.recipients) * 100)) : 100; },
                init() { if (!this.done) this.runBatch(); },
                resume() { this.paused = false; this.runBatch(); },
                async runBatch() {
                    if (this.paused || this.done) return;
                    try {
                        const { data } = await window.axios.post(this.batchUrl);
                        this.totals.sent = data.totals.sent;
                        this.totals.failed = data.totals.failed;
                        this.totals.skipped = data.totals.skipped;
                        if (data.done) { this.done = true; } else { this.runBatch(); }
                    } catch (e) {
                        this.error = 'Sending was interrupted. You can resume from the campaigns list.';
                        this.paused = true;
                    }
                }
            };
        }
    </script>
    @endpush
</x-layouts.dashboard>
