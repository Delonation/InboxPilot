<x-layouts.admin title="All users">
    <x-page-header title="All users" subtitle="Search and manage every registered account." />

    <x-card :pad="false">
        <form method="GET" class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1 max-w-sm">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400"><x-icon name="search" class="h-4 w-4" /></span>
                <input name="q" value="{{ $search }}" placeholder="Search name or email" class="form-input pl-9" />
            </div>
            <select name="status" class="form-select sm:w-48" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach(['pending','approved','rejected','suspended'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Search</button>
        </form>

        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>User</th><th>Status</th><th>Joined</th><th>Last login</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-gray-900 hover:underline">{{ $user->name }}</a>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </td>
                            <td><x-status-badge :status="$user->status" /></td>
                            <td class="text-gray-500">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="text-gray-500">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</td>
                            <td>@include('admin.users._actions', ['user' => $user])</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-sm text-gray-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 p-4">{{ $users->links() }}</div>
    </x-card>
</x-layouts.admin>
