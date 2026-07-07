<x-layouts.dashboard title="Edit template">
    <div class="mx-auto max-w-3xl">
        <x-page-header title="Edit template">
            <x-slot:actions>
                <a href="{{ route('templates.preview', $template) }}" target="_blank" class="btn-secondary"><x-icon name="eye" class="h-4 w-4" /> Preview</a>
            </x-slot:actions>
        </x-page-header>
        <x-card>
            @include('templates._form', ['template' => $template, 'action' => route('templates.update', $template), 'method' => 'PUT', 'submit' => 'Save changes'])
        </x-card>
    </div>
</x-layouts.dashboard>
