<x-layouts.admin title="Pending users">
    <x-page-header title="Pending users" subtitle="New registrations waiting for approval." />

    <x-card :pad="false">
        @if($users->count())
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>User</th><th>Registered</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-gray-900 hover:underline">{{ $user->name }}</a>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </td>
                                <td class="text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                                <td>@include('admin.users._actions', ['user' => $user])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 p-4">{{ $users->links() }}</div>
        @else
            <x-empty-state icon="check-circle" title="No pending users" message="Every registered account has been reviewed." />
        @endif
    </x-card>
</x-layouts.admin>
