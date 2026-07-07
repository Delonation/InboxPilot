@props(['title' => null, 'subtitle' => null, 'pad' => true])

<div {{ $attributes->class(['card']) }}>
    @if ($title || isset($actions))
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
            <div>
                @if ($title)
                    <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-xs text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $pad ? 'card-pad' : '' }}">
        {{ $slot }}
    </div>
</div>
