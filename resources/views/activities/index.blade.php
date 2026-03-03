@extends('layouts.app')

@section('content')
<div class="p-4 pb-24">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Saved Activities</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your Quick-Add exercises</p>
        </div>
        <button onclick="openModal('addActivityModal')" class="bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-full shadow-md transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 p-3 rounded-xl mb-4 text-sm text-center font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 p-3 rounded-xl mb-4 text-sm text-center font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse($activities as $activity)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-xl shrink-0">
                        {{ $activity->icon ?? '🏃' }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $activity->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Duration: {{ $activity->default_duration }} mins</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="editActivity({{ $activity->id }}, '{{ addslashes($activity->name) }}', {{ $activity->default_duration }}, '{{ $activity->icon }}')" class="text-blue-500 hover:text-blue-600 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>

                    <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" onsubmit="confirmActivityDelete(event, this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-600 p-2 bg-red-50 dark:bg-red-900/20 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                <span class="text-4xl">📭</span>
                <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">No saved activities yet.<br>Click the + button to add one.</p>
            </div>
        @endforelse
    </div>
</div>

<div id="addActivityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[60] flex items-end justify-center sm:items-center p-4 transition-opacity">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md overflow-hidden transform transition-all flex flex-col shadow-2xl">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/80">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-900 dark:text-white">Add New Activity</h3>
            <button onclick="closeModal('addActivityModal')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="activityForm" action="{{ route('activities.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="flex gap-4">
                <div class="w-1/4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
                    <input type="text" name="icon" id="activityIcon" placeholder="🏃‍♂️" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition-colors">
                </div>
                <div class="w-3/4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Activity Name</label>
                    <input type="text" name="name" id="activityName" placeholder="e.g. Morning Jog" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Default Duration (Minutes)</label>
                <input type="number" name="default_duration" id="activityDuration" value="15" min="1" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition-colors">
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl transition-colors mt-4 shadow-md">
                Save Activity
            </button>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        resetForm(); // Reset on close
    }

    function resetForm() {
        document.getElementById('modalTitle').innerText = 'Add New Activity';
        document.getElementById('activityForm').action = '{{ route('activities.store') }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('activityName').value = '';
        document.getElementById('activityDuration').value = '15';
        document.getElementById('activityIcon').value = '';
        document.getElementById('submitBtn').innerText = 'Save Activity';
    }

    function editActivity(id, name, duration, icon) {
        document.getElementById('modalTitle').innerText = 'Edit Activity';
        document.getElementById('activityForm').action = '/activities/' + id;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('activityName').value = name;
        document.getElementById('activityDuration').value = duration;
        document.getElementById('activityIcon').value = icon || '';
        document.getElementById('submitBtn').innerText = 'Update Activity';
        
        openModal('addActivityModal');
    }

    function confirmActivityDelete(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "This will remove the activity from your Quick-Add list.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endsection