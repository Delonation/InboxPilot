{{-- Shared template form. Expects $template and $action (route) + $method. --}}
<form method="POST" action="{{ $action }}" x-data="{ type: '{{ old('content_type', $template->content_type ?? 'html') }}' }" class="space-y-5">
    @csrf
    @if(($method ?? 'POST') !== 'POST')@method($method)@endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="form-label" for="name">Template name</label>
            <input id="name" name="name" type="text" class="form-input" value="{{ old('name', $template->name) }}" required />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>
        <div>
            <label class="form-label" for="content_type">Content type</label>
            <select id="content_type" name="content_type" class="form-select" x-model="type">
                <option value="html">HTML</option>
                <option value="plain">Plain text</option>
            </select>
        </div>
    </div>

    <div>
        <label class="form-label" for="subject">Subject</label>
        <input id="subject" name="subject" type="text" class="form-input" value="{{ old('subject', $template->subject) }}" required />
        <x-input-error :messages="$errors->get('subject')" class="mt-1" />
    </div>

    <div class="rounded-lg bg-gray-50 px-4 py-3 text-xs text-gray-600">
        <span class="font-medium text-gray-900">Placeholders:</span>
        @foreach(config('inboxpilot.placeholders') as $ph)
            <code class="mx-1 rounded bg-white px-1.5 py-0.5 ring-1 ring-gray-200">{{ '{'.'{'.$ph.'}'.'}' }}</code>
        @endforeach
    </div>

    <div x-show="type === 'html'">
        <label class="form-label" for="html_body">HTML body</label>
        <textarea id="html_body" name="html_body" rows="14" class="form-textarea font-mono text-xs">{{ old('html_body', $template->html_body) }}</textarea>
        <p class="form-hint">Script tags and inline event handlers are removed automatically for safety.</p>
        <x-input-error :messages="$errors->get('html_body')" class="mt-1" />
    </div>

    <div>
        <label class="form-label" for="plain_body">
            Plain text body <span class="text-gray-400" x-show="type === 'html'">(optional fallback)</span>
        </label>
        <textarea id="plain_body" name="plain_body" rows="8" class="form-textarea text-sm">{{ old('plain_body', $template->plain_body) }}</textarea>
        <x-input-error :messages="$errors->get('plain_body')" class="mt-1" />
    </div>

    <div class="flex justify-between">
        <a href="{{ route('templates.index') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">{{ $submit ?? 'Save template' }}</button>
    </div>
</form>
