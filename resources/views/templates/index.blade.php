<x-layouts.dashboard title="Templates">
    <x-page-header title="Templates" subtitle="Reusable email designs for your campaigns.">
        <x-slot:actions>
            <a href="{{ route('templates.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> New template</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-4 max-w-sm">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400"><x-icon name="search" class="h-4 w-4" /></span>
            <input name="q" value="{{ $search }}" placeholder="Search templates" class="form-input pl-9" />
        </div>
    </form>

    @if($templates->count())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($templates as $template)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $template->name }}</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($template->subject, 40) }}</p>
                        </div>
                        <x-badge :color="$template->isHtml() ? 'blue' : 'gray'">{{ strtoupper($template->content_type) }}</x-badge>
                    </div>
                    <div class="mt-4 flex items-center gap-1 border-t border-gray-100 pt-3">
                        <a href="{{ route('templates.edit', $template) }}" class="btn-ghost btn-sm"><x-icon name="pencil" class="h-4 w-4" /> Edit</a>
                        <a href="{{ route('templates.preview', $template) }}" target="_blank" class="btn-ghost btn-sm"><x-icon name="eye" class="h-4 w-4" /></a>
                        <form method="POST" action="{{ route('templates.duplicate', $template) }}">@csrf<button class="btn-ghost btn-sm"><x-icon name="duplicate" class="h-4 w-4" /></button></form>
                        <form method="POST" action="{{ route('templates.destroy', $template) }}" class="ml-auto" onsubmit="return confirm('Delete this template?')">@csrf @method('DELETE')<button class="btn-ghost btn-sm text-red-600"><x-icon name="trash" class="h-4 w-4" /></button></form>
                    </div>
                </x-card>
            @endforeach
        </div>
        <div class="mt-6">{{ $templates->links() }}</div>
    @else
        <x-card>
            <x-empty-state icon="document" title="No templates yet" message="Create your first email template to use in a campaign.">
                <x-slot:action><a href="{{ route('templates.create') }}" class="btn-primary">New template</a></x-slot:action>
            </x-empty-state>
        </x-card>
    @endif
</x-layouts.dashboard>
