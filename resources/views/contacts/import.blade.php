<x-layouts.dashboard title="Import contacts">
    <div class="mx-auto max-w-2xl">
        <x-page-header title="Import contacts" subtitle="Upload a CSV file. The email column is required." />

        <x-card>
            <form method="POST" action="{{ route('contacts.import.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label" for="file">CSV file</label>
                    <input id="file" name="file" type="file" accept=".csv,text/csv" class="form-input" required />
                    <p class="form-hint">Max {{ round(config('inboxpilot.csv.max_kb') / 1024, 1) }} MB. Allowed columns: email (required), first_name, last_name, phone, company, tags.</p>
                    <x-input-error :messages="$errors->get('file')" class="mt-1" />
                </div>

                <div class="rounded-lg bg-gray-50 p-4 text-xs text-gray-600">
                    <p class="font-medium text-gray-900">Example CSV header</p>
                    <code class="mt-1 block">email,first_name,last_name,company,tags</code>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Upload and import</button>
                </div>
            </form>
        </x-card>

        @if($recent->count())
            <x-card title="Recent imports" class="mt-6">
                <table class="table">
                    <thead><tr><th>File</th><th>Rows</th><th>Imported</th><th>Skipped</th><th>Invalid</th><th>When</th></tr></thead>
                    <tbody>
                        @foreach($recent as $imp)
                            <tr>
                                <td class="font-medium text-gray-900">{{ $imp->filename }}</td>
                                <td>{{ $imp->total_rows }}</td>
                                <td>{{ $imp->imported }}</td>
                                <td>{{ $imp->skipped_duplicates }}</td>
                                <td>{{ $imp->invalid_emails }}</td>
                                <td class="text-gray-500">{{ $imp->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        @endif
    </div>
</x-layouts.dashboard>
