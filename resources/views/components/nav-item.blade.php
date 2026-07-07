@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" {{ $attributes->class(['nav-item', 'nav-item-active' => $active]) }}>
    <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
    <span>{{ $slot }}</span>
</a>
