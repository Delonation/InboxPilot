@props(['title' => 'Nothing here yet', 'message' => null, 'icon' => 'inbox'])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
    @if ($message)
        <p class="mt-1 max-w-sm text-sm text-gray-500">{{ $message }}</p>
    @endif
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
