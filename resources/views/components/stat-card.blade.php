@props(['label', 'value', 'hint' => null, 'tone' => 'default'])

@php
    $valueTone = match ($tone) {
        'success' => 'text-green-600',
        'danger' => 'text-red-600',
        'warning' => 'text-amber-600',
        default => 'text-gray-900',
    };
@endphp

<div class="card card-pad">
    <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold {{ $valueTone }}">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
    @endif
</div>
