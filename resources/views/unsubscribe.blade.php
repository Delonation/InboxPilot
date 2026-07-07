<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unsubscribe · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans">
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="card card-pad text-center">
                @if($done)
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-600">
                        <x-icon name="check-circle" class="h-6 w-6" />
                    </div>
                    <h1 class="text-lg font-semibold text-gray-900">You have been unsubscribed</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ $contact->email }} will no longer receive campaigns from this sender.
                    </p>
                @else
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                        <x-icon name="mail" class="h-6 w-6" />
                    </div>
                    <h1 class="text-lg font-semibold text-gray-900">Unsubscribe from emails?</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Confirm that <span class="font-medium">{{ $contact->email }}</span> should no longer receive campaigns from this sender.
                    </p>
                    <form method="POST" action="{{ $action }}" class="mt-6">
                        @csrf
                        <button type="submit" class="btn-primary w-full">Unsubscribe me</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
