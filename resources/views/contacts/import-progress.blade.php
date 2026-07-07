<x-layouts.dashboard title="Importing contacts">
    <div class="mx-auto max-w-2xl"
         x-data="contactImport({
            batchUrl: '{{ route('contacts.import.batch', $import) }}',
            total: {{ $import->total_rows }},
            alreadyDone: {{ $import->status === 'completed' ? 'true' : 'false' }},
            initialSummary: {
                imported: {{ $import->imported }},
                skipped_duplicates: {{ $import->skipped_duplicates }},
                invalid_emails: {{ $import->invalid_emails }},
                failed_rows: {{ $import->failed_rows }}
            }
         })">
        <x-page-header title="Importing contacts" :subtitle="$import->filename" />

        <x-card>
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-medium text-gray-900" x-text="done ? 'Import complete' : 'Importing...'"></span>
                <span class="text-gray-500"><span x-text="offset"></span> / <span x-text="total"></span> rows</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                <div class="h-2 rounded-full bg-gray-900 transition-all" :style="`width: ${pct}%`"></div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-xl font-semibold text-green-600" x-text="summary.imported"></p>
                    <p class="text-xs text-gray-500">Imported</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-xl font-semibold text-gray-700" x-text="summary.skipped_duplicates"></p>
                    <p class="text-xs text-gray-500">Duplicates</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-xl font-semibold text-amber-600" x-text="summary.invalid_emails"></p>
                    <p class="text-xs text-gray-500">Invalid</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-xl font-semibold text-red-600" x-text="summary.failed_rows"></p>
                    <p class="text-xs text-gray-500">Failed</p>
                </div>
            </div>

            <div x-show="error" x-cloak class="mt-4"><x-alert type="error" x-text="error"></x-alert></div>

            <div class="mt-6 flex justify-end gap-2" x-show="done" x-cloak>
                <a href="{{ route('contacts.import.create') }}" class="btn-secondary">Import another</a>
                <a href="{{ route('contacts.index') }}" class="btn-primary">View contacts</a>
            </div>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function contactImport(config) {
            return {
                batchUrl: config.batchUrl,
                total: config.total,
                offset: config.alreadyDone ? config.total : 0,
                done: config.alreadyDone,
                summary: config.initialSummary,
                error: null,
                get pct() { return this.total ? Math.min(100, Math.round((this.offset / this.total) * 100)) : 100; },
                init() { if (!this.done) this.runBatch(); },
                async runBatch() {
                    try {
                        const { data } = await window.axios.post(this.batchUrl, { offset: this.offset });
                        this.offset = data.offset;
                        this.summary = data.summary;
                        if (data.done) { this.done = true; } else { this.runBatch(); }
                    } catch (e) {
                        this.error = 'The import was interrupted. Please try uploading the file again.';
                    }
                }
            };
        }
    </script>
    @endpush
</x-layouts.dashboard>
