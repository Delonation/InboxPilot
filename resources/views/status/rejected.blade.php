<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <x-icon name="x-circle" class="h-6 w-6" />
        </div>
        <h1 class="text-lg font-semibold text-gray-900">Account not approved</h1>
        <p class="mt-2 text-sm text-gray-600">
            Your account was not approved for access. If you believe this is a mistake, please contact the
            site administrator.
        </p>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="btn-secondary w-full">Log out</button>
    </form>
</x-guest-layout>
