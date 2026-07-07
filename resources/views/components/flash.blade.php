{{-- Renders session flash messages set via ->with('success'|'error'|'warning'|'status', ...) --}}
@if (session('success'))
    <div class="mb-4"><x-alert type="success">{{ session('success') }}</x-alert></div>
@endif

@if (session('error'))
    <div class="mb-4"><x-alert type="error">{{ session('error') }}</x-alert></div>
@endif

@if (session('warning'))
    <div class="mb-4"><x-alert type="warning">{{ session('warning') }}</x-alert></div>
@endif

@if (session('status'))
    <div class="mb-4"><x-alert type="info">{{ session('status') }}</x-alert></div>
@endif
