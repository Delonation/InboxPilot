<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <x-icon name="warning" class="h-6 w-6" />
        </div>
        <h1 class="text-lg font-semibold text-gray-900">Account suspended</h1>
        <p class="mt-2 text-sm text-gray-600">
            Your account has been suspended and you cannot use the app right now.
        </p>
        @if (!empty($reason))
            <div class="mt-4 rounded-lg bg-gray-50 px-4 py-3 text-left text-sm text-gray-700">
                <span class="font-medium">Reason:</span> {{ $reason }}
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="btn-secondary w-full">Log out</button>
    </form>
</x-guest-layout>
