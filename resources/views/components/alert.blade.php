@props(['type' => 'info', 'title' => null])

@php
    $styles = match ($type) {
        'success' => ['bg-green-50 border-green-200 text-green-800', 'text-green-500'],
        'error' => ['bg-red-50 border-red-200 text-red-800', 'text-red-500'],
        'warning' => ['bg-amber-50 border-amber-200 text-amber-800', 'text-amber-500'],
        default => ['bg-blue-50 border-blue-200 text-blue-800', 'text-blue-500'],
    };
@endphp

<div {{ $attributes->class(['rounded-lg border px-4 py-3 text-sm', $styles[0]]) }}>
    @if ($title)
        <p class="font-semibold">{{ $title }}</p>
    @endif
    <div class="{{ $title ? 'mt-1' : '' }}">{{ $slot }}</div>
</div>
