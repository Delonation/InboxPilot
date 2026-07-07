@props(['color' => 'gray'])

@php
    $class = match ($color) {
        'green' => 'badge-green',
        'red' => 'badge-red',
        'amber' => 'badge-amber',
        'blue' => 'badge-blue',
        default => 'badge-gray',
    };
@endphp

<span {{ $attributes->class([$class]) }}>{{ $slot }}</span>
