<x-layouts.dashboard title="New campaign">
    <div class="mx-auto max-w-3xl" x-data="{ selection: 'all', confirm: false }">
        <x-page-header title="New campaign" subtitle="Pick a template and audience, then send." />

        @if($templates->isEmpty())
            <x-card><x-empty-state icon="document" title="You need a template first" message="Create an email template before sending a campaign.">
                <x-slot:action><a href="{{ route('templates.create') }}" class="btn-primary">Create template</a></x-slot:action>
            </x-empty-state></x-card>
        @else
            <form method="POST" action="{{ route('campaigns.store') }}">
                @csrf
                <x-card title="Campaign details">
                    <div class="space-y-4">
                        <div>
                            <label class="form-label" for="name">Campaign name</label>
                            <input id="name" name="name" type="text" class="form-input" value="{{ old('name') }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <label class="form-label" for="template_id">Template</label>
                            <select id="template_id" name="template_id" class="form-select" required>
                                <option value="">Select a template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>{{ $template->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('template_id')" class="mt-1" />
                        </div>
                        <div>
                            <label class="form-label" for="subject_override">Subject override <span class="text-gray-400">(optional)</span></label>
                            <input id="subject_override" name="subject_override" type="text" class="form-input" value="{{ old('subject_override') }}" placeholder="Leave blank to use the template subject" />
                        </div>
                    </div>
                </x-card>

                <x-card title="Audience" class="mt-6">
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer" :class="selection==='all' && 'ring-2 ring-gray-900'">
                            <input type="radio" name="selection" value="all" x-model="selection" class="mt-0.5 text-gray-900 focus:ring-gray-900" checked>
                            <span>
                                <span class="block text-sm font-medium text-gray-900">All active contacts</span>
                                <span class="block text-xs text-gray-500">{{ number_format($activeContacts) }} contacts who have not unsubscribed.</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer" :class="selection==='tag' && 'ring-2 ring-gray-900'">
                            <input type="radio" name="selection" value="tag" x-model="selection" class="mt-0.5 text-gray-900 focus:ring-gray-900">
                            <span class="flex-1">
                                <span class="block text-sm font-medium text-gray-900">Contacts with a tag</span>
                                <div x-show="selection==='tag'" x-cloak class="mt-2">
                                    @if(count($tags))
                                        <select name="tag" class="form-select">
                                            @foreach($tags as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                                        </select>
                                    @else
                                        <span class="text-xs text-gray-500">You have no tagged contacts yet.</span>
                                    @endif
                                </div>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer" :class="selection==='selected' && 'ring-2 ring-gray-900'">
                            <input type="radio" name="selection" value="selected" x-model="selection" class="mt-0.5 text-gray-900 focus:ring-gray-900">
                            <span class="flex-1">
                                <span class="block text-sm font-medium text-gray-900">Choose specific contacts</span>
                                <div x-show="selection==='selected'" x-cloak class="mt-2 max-h-56 overflow-y-auto rounded-lg border border-gray-200">
                                    @forelse($pickable as $c)
                                        <label class="flex items-center gap-2 border-b border-gray-100 px-3 py-2 text-sm last:border-0">
                                            <input type="checkbox" name="contact_ids[]" value="{{ $c->id }}" class="rounded border-gray-300 text-gray-900">
                                            <span>{{ $c->email }} <span class="text-gray-400">{{ trim($c->first_name.' '.$c->last_name) }}</span></span>
                                        </label>
                                    @empty
                                        <p class="px-3 py-2 text-xs text-gray-500">No active contacts.</p>
                                    @endforelse
                                    @if($pickable->count() === 500)
                                        <p class="px-3 py-2 text-xs text-amber-600">Showing the first 500 contacts. Use a tag or "All active contacts" for larger lists.</p>
                                    @endif
                                </div>
                            </span>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('selection')" class="mt-2" />
                </x-card>

                <div class="mt-4"><x-alert type="info">InboxPilot confirms whether your SMTP server accepted each message. It cannot guarantee inbox placement.</x-alert></div>

                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('campaigns.index') }}" class="btn-secondary">Cancel</a>
                    <button type="button" @click="confirm = true" class="btn-primary">Review and send</button>
                </div>

                {{-- Confirmation --}}
                <div x-show="confirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 p-4">
                    <div @click.outside="confirm = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                        <h3 class="text-base font-semibold text-gray-900">Send this campaign?</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Sending starts immediately and runs in your browser. Keep this tab open until it finishes.
                            Unsubscribed contacts are skipped automatically.
                        </p>
                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="confirm = false" class="btn-secondary">Back</button>
                            <button type="submit" class="btn-primary">Send now</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-layouts.dashboard>
