<x-layouts.dashboard title="New template">
    <div class="mx-auto max-w-3xl">
        <x-page-header title="New template" />
        <x-card>
            @include('templates._form', ['template' => $template, 'action' => route('templates.store'), 'submit' => 'Create template'])
        </x-card>
    </div>
</x-layouts.dashboard>
