@extends('layouts.app')

@section('content')
<div class="p-4 pb-24">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">User Management</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage registered users</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 p-3 rounded-xl mb-4 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 p-3 rounded-xl mb-4 text-sm text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($users as $user)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base">{{ $user->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">Joined: {{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        @if($user->is_suspended)
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-md font-medium">Suspended</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-md font-medium">Active</span>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100 dark:border-gray-700 mt-2">
                    <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors {{ $user->is_suspended ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-orange-100 text-orange-700 hover:bg-orange-200' }}">
                            {{ $user->is_suspended ? 'Activate' : 'Suspend' }}
                        </button>
                    </form>

                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="confirmUserDelete(event, this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400">No registered users found.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $users->links('pagination::tailwind') }}
    </div>
</div>

<script>
    // NEW: SweetAlert function for high-risk user deletion confirmation
    function confirmUserDelete(event, form) {
        event.preventDefault(); // Stop immediate form submission
        
        Swal.fire({
            title: 'WARNING: Delete User?',
            text: "This will permanently delete the user and ALL their data. Are you sure?",
            icon: 'error', // using error icon for high destructive actions
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // red-500
            cancelButtonColor: '#6b7280', // gray-500
            confirmButtonText: 'Yes, delete permanently!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit the form if confirmed
            }
        });
    }
</script>
@endsection