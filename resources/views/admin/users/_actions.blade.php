{{-- Contextual admin actions for a user. Expects $user. --}}
<div class="flex flex-wrap items-center justify-end gap-2" x-data="{ suspend: false }">
    @if($user->status !== 'approved')
        <form method="POST" action="{{ route('admin.users.approve', $user) }}">@csrf<button class="btn-primary btn-sm">Approve</button></form>
    @endif

    @if($user->status === 'pending')
        <form method="POST" action="{{ route('admin.users.reject', $user) }}" onsubmit="return confirm('Reject this user?')">@csrf<button class="btn-secondary btn-sm">Reject</button></form>
    @endif

    @if($user->status === 'approved')
        <button @click="suspend = true" type="button" class="btn-secondary btn-sm">Suspend</button>
    @endif

    @if($user->status === 'suspended')
        <form method="POST" action="{{ route('admin.users.reactivate', $user) }}">@csrf<button class="btn-secondary btn-sm">Reactivate</button></form>
    @endif

    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete this user and all their data?')">
        @csrf @method('DELETE')
        <button class="btn-ghost btn-sm text-red-600"><x-icon name="trash" class="h-4 w-4" /></button>
    </form>

    {{-- Suspend reason modal --}}
    <div x-show="suspend" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 p-4">
        <div @click.outside="suspend = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl text-left">
            <h3 class="text-base font-semibold text-gray-900">Suspend {{ $user->name }}</h3>
            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="mt-4 space-y-3">
                @csrf
                <textarea name="reason" rows="3" class="form-textarea" placeholder="Reason (optional, shown to the user)"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="suspend = false" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-danger">Suspend</button>
                </div>
            </form>
        </div>
    </div>
</div>
