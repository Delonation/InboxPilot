<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview · {{ $template->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 p-6">
    <div class="mx-auto max-w-3xl">
        <div class="mb-4">
            <p class="text-xs uppercase tracking-wide text-gray-400">Preview with sample data</p>
            <h1 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h1>
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50 px-5 py-3 text-sm">
                <span class="text-gray-500">Subject:</span>
                <span class="font-medium text-gray-900">{{ $subject }}</span>
            </div>
            <div class="bg-white">
                @if($html !== null)
                    {{-- Rendered inside a sandboxed iframe; content was sanitized server-side. --}}
                    <iframe sandbox="allow-same-origin" class="h-[70vh] w-full" srcdoc="{{ $html }}"></iframe>
                @else
                    <pre class="whitespace-pre-wrap p-5 text-sm text-gray-800">{{ $text }}</pre>
                @endif
            </div>
        </div>

        <p class="mt-4 text-center text-xs text-gray-400">This is a preview. Placeholders are filled with example values.</p>
    </div>
</body>
</html>
