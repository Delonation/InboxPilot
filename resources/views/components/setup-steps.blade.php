@props(['current', 'index'])

@php
    $labels = ['profile' => 'Profile', 'sender' => 'Sender', 'smtp' => 'SMTP', 'test' => 'Test email', 'done' => 'Done'];
    $i = 0;
@endphp

<ol class="mb-8 flex flex-wrap items-center gap-2 text-sm">
    @foreach($labels as $key => $label)
        @php $state = $i < $index ? 'done' : ($i === $index ? 'current' : 'todo'); $i++; @endphp
        <li class="flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold
                {{ $state === 'done' ? 'bg-green-100 text-green-700' : ($state === 'current' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-400') }}">
                @if($state === 'done')<x-icon name="check" class="h-3.5 w-3.5" />@else{{ $loop->iteration }}@endif
            </span>
            <span class="{{ $state === 'current' ? 'font-medium text-gray-900' : 'text-gray-500' }}">{{ $label }}</span>
            @unless($loop->last)<x-icon name="chevron-right" class="h-4 w-4 text-gray-300" />@endunless
        </li>
    @endforeach
</ol>
