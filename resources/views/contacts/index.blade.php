<x-layouts.dashboard title="Contacts">
    <x-page-header title="Contacts" :subtitle="number_format($total).' total'">
        <x-slot:actions>
            <a href="{{ route('contacts.export') }}" class="btn-secondary"><x-icon name="download" class="h-4 w-4" /> Export</a>
            <a href="{{ route('contacts.import.create') }}" class="btn-secondary"><x-icon name="upload" class="h-4 w-4" /> Import CSV</a>
            <button type="button" onclick="document.getElementById('add-contact').showModal()" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add contact</button>
        </x-slot:actions>
    </x-page-header>

    <x-card :pad="false">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex flex-1 items-center gap-2">
                <div class="relative flex-1 max-w-sm">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input name="q" value="{{ $search }}" placeholder="Search email, name, company" class="form-input pl-9" />
                </div>
                @if($tag)<input type="hidden" name="tag" value="{{ $tag }}">@endif
                <button class="btn-secondary">Search</button>
                @if($search || $tag)<a href="{{ route('contacts.index') }}" class="btn-ghost btn-sm">Clear</a>@endif
            </form>
            @if($tag)<span class="badge-blue">Tag: {{ $tag }}</span>@endif
        </div>

        @if($contacts->count())
            <form method="POST" action="{{ route('contacts.bulkDestroy') }}" x-data="{ selected: [] }"
                  onsubmit="return confirm('Delete the selected contacts?')">
                @csrf
                <div x-show="selected.length" x-cloak class="flex items-center justify-between bg-gray-50 px-4 py-2 text-sm">
                    <span><span x-text="selected.length"></span> selected</span>
                    <button class="btn-danger btn-sm">Delete selected</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-10"><input type="checkbox" @change="selected = $event.target.checked ? [...document.querySelectorAll('.row-cb')].map(c => c.value) : []" class="rounded border-gray-300"></th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Tags</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $contact->id }}" x-model="selected" class="row-cb rounded border-gray-300"></td>
                                    <td class="font-medium text-gray-900">{{ $contact->email }}</td>
                                    <td>{{ $contact->fullName() ?: '—' }}</td>
                                    <td>{{ $contact->company ?: '—' }}</td>
                                    <td>
                                        @forelse($contact->tagList() as $t)
                                            <a href="{{ route('contacts.index', ['tag' => $t]) }}" class="badge-gray mr-1">{{ $t }}</a>
                                        @empty <span class="text-gray-400">—</span> @endforelse
                                    </td>
                                    <td>
                                        @if($contact->is_unsubscribed)<x-badge color="red">Unsubscribed</x-badge>@else<x-badge color="green">Active</x-badge>@endif
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('contacts.edit', $contact) }}" class="btn-ghost btn-sm"><x-icon name="pencil" class="h-4 w-4" /></a>
                                            <button type="button" onclick="if(confirm('Delete this contact?')){document.getElementById('del-{{ $contact->id }}').submit()}" class="btn-ghost btn-sm text-red-600"><x-icon name="trash" class="h-4 w-4" /></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            @foreach($contacts as $contact)
                <form id="del-{{ $contact->id }}" method="POST" action="{{ route('contacts.destroy', $contact) }}" class="hidden">@csrf @method('DELETE')</form>
            @endforeach

            <div class="border-t border-gray-100 p-4">{{ $contacts->links() }}</div>
        @else
            <x-empty-state icon="contacts" title="No contacts yet" message="Import a CSV or add a contact manually to get started.">
                <x-slot:action>
                    <a href="{{ route('contacts.import.create') }}" class="btn-primary">Import CSV</a>
                </x-slot:action>
            </x-empty-state>
        @endif
    </x-card>

    {{-- Add contact modal --}}
    <dialog id="add-contact" class="rounded-xl p-0 backdrop:bg-gray-900/40">
        <form method="POST" action="{{ route('contacts.store') }}" class="w-[26rem] max-w-full p-6">
            @csrf
            <h3 class="text-base font-semibold text-gray-900">Add contact</h3>
            <div class="mt-4 space-y-3">
                <div>
                    <label class="form-label" for="c_email">Email</label>
                    <input id="c_email" name="email" type="email" class="form-input" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <input name="first_name" type="text" class="form-input" placeholder="First name" />
                    <input name="last_name" type="text" class="form-input" placeholder="Last name" />
                </div>
                <input name="company" type="text" class="form-input" placeholder="Company" />
                <input name="phone" type="text" class="form-input" placeholder="Phone" />
                <input name="tags" type="text" class="form-input" placeholder="Tags (comma separated)" />
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-contact').close()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Add contact</button>
            </div>
        </form>
    </dialog>

    @if($errors->any() && old('email'))
        <script>document.getElementById('add-contact').showModal();</script>
    @endif
</x-layouts.dashboard>
