<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
            <x-icon name="clock" class="h-6 w-6" />
        </div>
        <h1 class="text-lg font-semibold text-gray-900">Your account is awaiting approval</h1>
        <p class="mt-2 text-sm text-gray-600">
            Thanks for registering, {{ auth()->user()->name }}. An administrator needs to approve your
            account before you can connect SMTP, import contacts, or send campaigns. You will be able to
            sign in and start once your account is approved.
        </p>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="btn-secondary w-full">Log out</button>
    </form>
</x-guest-layout>
