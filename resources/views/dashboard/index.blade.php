@extends('layouts.app')

@section('content')
<div id="offline-toast" class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-yellow-500 text-white text-sm font-bold px-4 py-2 rounded-full shadow-lg transition-opacity duration-300 opacity-0 pointer-events-none z-[70] flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
    <span id="offline-toast-msg">Saved offline. Will sync soon.</span>
</div>

<div class="p-4">

    <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl shadow-sm p-3 mb-4">
        <a href="{{ route('dashboard', ['date' => $prevDate]) }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        
        <form id="dateForm" action="{{ route('dashboard') }}" method="GET" class="relative">
            <input type="date" name="date" id="datePicker" value="{{ $currentDate->toDateString() }}" 
                   max="{{ \Carbon\Carbon::today(auth()->user()->timezone)->toDateString() }}"
                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                   onchange="document.getElementById('dateForm').submit();">
            <div class="text-center cursor-pointer pointer-events-none">
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    @if($currentDate->isToday(auth()->user()->timezone)) {{ __('messages.today') ?? 'Today' }} 
                    @elseif($currentDate->isYesterday()) {{ __('messages.yesterday') ?? 'Yesterday' }} 
                    @else {{ $currentDate->format('l') }} @endif
                </p>
                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">
                    {{ $currentDate->format('d M, Y') }}
                </p>
            </div>
        </form>

        @if(!$currentDate->isToday(auth()->user()->timezone))
        <a href="{{ route('dashboard', ['date' => $nextDate]) }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        @else
        <div class="w-9 h-9"></div> @endif
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 p-3 rounded-xl mb-4 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 p-3 rounded-xl mb-4 text-sm text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-4 grid grid-cols-3 gap-3">
        <label class="flex flex-col items-center justify-center p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 cursor-pointer">
            <span class="text-xl mb-1">🌙</span>
            <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Fasting</span>
            <input type="checkbox" id="fastingToggle" class="mt-1 w-4 h-4 text-primary focus:ring-primary rounded cursor-pointer" {{ $dailyMetric->is_fasting ? 'checked' : '' }} onchange="handleFastingToggle(this)">
        </label>
        
        <div class="flex flex-col items-center justify-center p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
            <span class="text-xl mb-1">💤</span>
            <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1">Sleep (Hrs)</span>
            <div class="flex items-center border border-gray-300 dark:border-gray-500 rounded-md overflow-hidden bg-white dark:bg-gray-600">
                <button type="button" onclick="changeSleep(-0.5)" class="w-6 h-6 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">-</button>
                <input type="number" id="sleepInput" step="0.5" min="0" value="{{ $dailyMetric->sleep_hours }}" readonly class="w-8 h-6 px-1 text-xs text-center border-none focus:ring-0 dark:bg-gray-600 dark:text-white pointer-events-none m-0 p-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none" style="-moz-appearance: textfield;">
                <button type="button" onclick="changeSleep(0.5)" class="w-6 h-6 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">+</button>
            </div>
        </div>

        <div class="flex flex-col items-center justify-center p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
            <span class="text-xl mb-1">🧠</span>
            <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Mood</span>
            <select onchange="updateMetric('mood', this.value)" class="mt-1 w-full text-xs p-1 border rounded dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                <option value="">Select</option>
                <option value="Great" {{ $dailyMetric->mood == 'Great' ? 'selected' : '' }}>🤩 Great</option>
                <option value="Good" {{ $dailyMetric->mood == 'Good' ? 'selected' : '' }}>🙂 Good</option>
                <option value="Okay" {{ $dailyMetric->mood == 'Okay' ? 'selected' : '' }}>😐 Okay</option>
                <option value="Bad" {{ $dailyMetric->mood == 'Bad' ? 'selected' : '' }}>🙁 Bad</option>
            </select>
        </div>
    </div>

    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl shadow-sm p-4 mb-4 border border-indigo-100 dark:border-indigo-800">
        <h3 class="text-sm font-bold text-indigo-800 dark:text-indigo-300 mb-3 flex items-center gap-1">
            🕌 Daily Prayers
        </h3>
        <div class="grid grid-cols-3 gap-2">
            @php
                $prayers = [
                    'fajr' => 'Fajr', 'dhuhr' => 'Dhuhr', 'asr' => 'Asr', 
                    'maghrib' => 'Maghrib', 'isha' => 'Isha', 'tahajjud' => 'Tahajjud'
                ];
            @endphp
            @foreach($prayers as $key => $name)
                @php $isPrayed = $prayerLog->$key; @endphp
                <div class="relative flex items-center bg-white dark:bg-gray-800 p-2 rounded-lg border {{ $isPrayed ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-200 dark:border-gray-700' }} transition-colors">
                    <input type="checkbox" id="prayer_{{ $key }}" class="hidden" {{ $isPrayed ? 'checked' : '' }} onchange="togglePrayer('{{ $key }}', this.checked)">
                    <label for="prayer_{{ $key }}" class="flex items-center w-full cursor-pointer">
                        <div id="prayer_circle_{{ $key }}" class="w-5 h-5 flex-shrink-0 flex items-center justify-center rounded-full border-2 transition-all mr-2 {{ $isPrayed ? 'bg-indigo-500 border-indigo-500' : 'border-indigo-200 dark:border-indigo-700' }}">
                            <svg class="w-3 h-3 text-white {{ $isPrayed ? 'block' : 'hidden' }} prayer-icon-{{ $key }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $name }}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl shadow-sm p-4 mb-4 border border-orange-100 dark:border-orange-800">
        <h3 class="text-sm font-bold text-orange-800 dark:text-orange-300 mb-3 flex items-center gap-1">
            🏃‍♂️ Exercise & Activity
        </h3>
        
        <form action="{{ route('exercise.log') }}" method="POST" class="flex gap-2 mb-3">
            @csrf
            <input type="hidden" name="date" value="{{ $currentDate->toDateString() }}">
            <input type="text" name="exercise_name" placeholder="e.g. Walking, Gym" required class="flex-1 px-3 py-2 border border-orange-200 rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <input type="number" name="duration_minutes" placeholder="Mins" required min="1" class="w-20 px-3 py-2 border border-orange-200 rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition">Add</button>
        </form>

        <div class="flex overflow-x-auto gap-2 pb-2 mb-2 no-scrollbar">
            @if(isset($savedActivities) && $savedActivities->count() > 0)
                @foreach($savedActivities as $activity)
                    <button type="button" onclick="quickAddExercise('{{ addslashes($activity->name) }}', {{ $activity->default_duration }})" 
                            class="shrink-0 bg-white dark:bg-gray-700 border border-orange-200 dark:border-orange-800 text-gray-700 dark:text-gray-200 text-xs px-3 py-1.5 rounded-full hover:bg-orange-50 dark:hover:bg-orange-900/30 transition shadow-sm flex items-center gap-1">
                        <span>{{ $activity->icon }}</span> {{ $activity->name }} ({{ $activity->default_duration }}m)
                    </button>
                @endforeach
            @endif
            <a href="{{ route('activities.index') }}" class="shrink-0 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs px-3 py-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm flex items-center">
                ⚙️ Manage
            </a>
        </div>

        <div class="space-y-2 mt-2">
            @forelse($exerciseLogs as $exercise)
                <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-orange-100 dark:border-gray-700 shadow-sm">
                    <div>
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $exercise->exercise_name }}</span>
                        <span class="text-xs text-orange-600 dark:text-orange-400 font-medium ml-2">{{ $exercise->duration_minutes }} mins</span>
                    </div>
                    <form action="{{ route('exercise.delete', $exercise->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-red-400 hover:text-red-600 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-orange-400 italic text-center py-2">No exercise logged today.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl shadow-sm p-4 mb-4 border border-blue-100 dark:border-blue-800">
        <div class="flex justify-between items-center mb-3">
            <span class="text-sm font-bold text-blue-800 dark:text-blue-300 flex items-center gap-1">
                💧 Daily Water (8 Glasses)
            </span>
            <span id="water_text" class="text-xs font-bold text-blue-600 dark:text-blue-400">
                {{ $waterGlasses }}/8
            </span>
        </div>
        <div class="flex justify-between" id="water-container">
            @for($i = 1; $i <= 8; $i++)
                <button onclick="toggleWater(this, {{ $i }})" data-filled="{{ $i <= $waterGlasses ? 'true' : 'false' }}" class="w-8 h-10 rounded-b-lg border-2 transition-all duration-300 {{ $i <= $waterGlasses ? 'bg-blue-400 border-blue-500 shadow-inner' : 'bg-white dark:bg-gray-700 border-blue-200 dark:border-blue-700' }}">
                </button>
            @endfor
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-4 grid grid-cols-4 gap-2 text-center divide-x dark:divide-gray-700">
        <div>
            <div class="text-[10px] text-gray-500 uppercase tracking-wider">Calories</div>
            <div class="font-bold text-gray-800 dark:text-gray-200">{{ number_format($macros['calories']) }}</div>
        </div>
        <div>
            <div class="text-[10px] text-gray-500 uppercase tracking-wider">Protein</div>
            <div class="font-bold text-blue-500">{{ number_format($macros['protein']) }}g</div>
        </div>
        <div>
            <div class="text-[10px] text-gray-500 uppercase tracking-wider">Carbs</div>
            <div class="font-bold text-emerald-500">{{ number_format($macros['carbs']) }}g</div>
        </div>
        <div>
            <div class="text-[10px] text-gray-500 uppercase tracking-wider">Fats</div>
            <div class="font-bold text-orange-500">{{ number_format($macros['fats']) }}g</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Food & Supplements</span>
            <span id="progress_text" class="text-xs font-bold text-primary">
                {{ $completedItems }}/{{ $totalItems }} Done
            </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
            <div id="progress_bar" class="bg-primary h-2.5 rounded-full transition-all duration-500 ease-out" style="width: {{ $totalItems > 0 ? ($completedItems / $totalItems) * 100 : 0 }}%"></div>
        </div>
    </div>

    <div class="space-y-6 pb-6">
        @foreach($groupedItems as $category => $categoryItems)
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 ml-2">{{ $category }}</h3>
                <div class="space-y-3">
                    @foreach($categoryItems as $item)
                        @php $isChecked = in_array($item->id, $logs); @endphp
                        <div class="flex items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-transparent transition-colors {{ $isChecked ? 'border-primary bg-emerald-50 dark:bg-emerald-900/20' : '' }}">
                            
                            <div class="mr-4 flex-shrink-0">
                                <input type="checkbox" id="item_{{ $item->id }}" class="peer hidden" {{ $isChecked ? 'checked' : '' }} onchange="toggleItem({{ $item->id }}, '{{ $currentDate->toDateString() }}')">
                                <label for="item_{{ $item->id }}" class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 dark:border-gray-600 cursor-pointer peer-checked:bg-primary peer-checked:border-primary transition-all">
                                    <svg class="w-5 h-5 text-white {{ $isChecked ? 'block' : 'hidden' }} check-icon-{{ $item->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </label>
                            </div>

                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <span>{{ $item->icon }}</span> {{ $item->name }}
                                    </h3>
                                    <span id="stock_badge_{{ $item->id }}" class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 {{ $item->stock <= 5 ? 'text-red-500 bg-red-100 dark:bg-red-900/30' : '' }}">
                                        Stock: <span id="stock_count_{{ $item->id }}">{{ $item->stock }}</span> {{ $item->unit }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ $item->instruction }}</p>
                                
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-xs font-semibold text-primary">{{ $item->timing }}</p>
                                    
                                    <div class="flex items-center gap-2">
                                        <label for="qty_{{ $item->id }}" class="text-xs text-gray-500 dark:text-gray-400">Qty:</label>
                                        <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-md overflow-hidden bg-white dark:bg-gray-700">
                                            <button type="button" id="btn_dec_{{ $item->id }}" onclick="changeQty({{ $item->id }}, -1)" class="w-6 h-6 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" {{ $isChecked ? 'disabled' : '' }}>-</button>
                                            <input type="number" id="qty_{{ $item->id }}" value="1" min="1" class="w-8 h-6 px-1 text-xs text-center border-none focus:ring-0 dark:bg-gray-700 dark:text-white [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none m-0" {{ $isChecked ? 'disabled' : '' }} style="-moz-appearance: textfield;">
                                            <button type="button" id="btn_inc_{{ $item->id }}" onclick="changeQty({{ $item->id }}, 1)" class="w-6 h-6 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" {{ $isChecked ? 'disabled' : '' }}>+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const selectedDate = '{{ $currentDate->toDateString() }}';

    function showOfflineToast(message) {
        let toast = document.getElementById('offline-toast');
        if(message) document.getElementById('offline-toast-msg').innerText = message;
        toast.classList.remove('opacity-0');
        setTimeout(() => { toast.classList.add('opacity-0'); }, 3000);
    }

    // --- NEW: Sleep Increment/Decrement ---
    function changeSleep(change) {
        let input = document.getElementById('sleepInput');
        let currentVal = parseFloat(input.value) || 0;
        let newVal = currentVal + change;
        if (newVal < 0) newVal = 0;
        input.value = newVal;
        updateMetric('sleep_hours', newVal);
    }

    // --- NEW: Quick Add Exercise ---
    function quickAddExercise(name, duration) {
        const form = document.querySelector('form[action="{{ route('exercise.log') }}"]');
        const nameInput = form.querySelector('input[name="exercise_name"]');
        const durationInput = form.querySelector('input[name="duration_minutes"]');
        
        nameInput.value = name;
        durationInput.value = duration;
        form.submit();
    }

    // --- UPDATED: Toggle Prayers UI with SweetAlert Confirmation ---
    function togglePrayer(prayerName, isChecked) {
        let checkbox = document.getElementById('prayer_' + prayerName);

        if (!isChecked) {
            // Revert visually while waiting for confirmation
            checkbox.checked = true;
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to unmark this prayer?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6366f1', // indigo-500 to match the prayer UI
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unmark'
            }).then((result) => {
                if (result.isConfirmed) {
                    checkbox.checked = false;
                    executePrayerToggle(prayerName, false);
                }
            });
        } else {
            executePrayerToggle(prayerName, true);
        }
    }

    function executePrayerToggle(prayerName, isChecked) {
        let icon = document.querySelector('.prayer-icon-' + prayerName);
        let parentDiv = document.getElementById('prayer_' + prayerName).closest('.relative');
        let circleDiv = document.getElementById('prayer_circle_' + prayerName);

        if(isChecked) {
            icon.classList.remove('hidden');
            parentDiv.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
            circleDiv.classList.add('bg-indigo-500', 'border-indigo-500');
            circleDiv.classList.remove('border-indigo-200', 'dark:border-indigo-700');
        } else {
            icon.classList.add('hidden');
            parentDiv.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
            circleDiv.classList.remove('bg-indigo-500', 'border-indigo-500');
            circleDiv.classList.add('border-indigo-200', 'dark:border-indigo-700');
        }

        fetch('{{ route('prayer.toggle') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ prayer: prayerName, status: isChecked ? 1 : 0, date: selectedDate })
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success && !data.offline) {
                Swal.fire('Error', 'Error updating prayer.', 'error');
                // Revert check state on error
                document.getElementById('prayer_' + prayerName).checked = !isChecked;
                executePrayerToggle(prayerName, !isChecked); 
            }
        });
    }

    // --- UPDATED: Fasting Toggle with SweetAlert ---
    function handleFastingToggle(checkbox) {
        let isChecked = checkbox.checked;

        if (!isChecked) {
            // Revert visually while waiting for confirmation
            checkbox.checked = true;
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to unmark your fasting status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981', // primary color
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unmark'
            }).then((result) => {
                if (result.isConfirmed) {
                    checkbox.checked = false;
                    updateMetric('is_fasting', 0);
                }
            });
        } else {
            updateMetric('is_fasting', 1);
        }
    }

    function updateMetric(field, value) {
        let bodyData = { date: selectedDate };
        bodyData[field] = value;

        fetch('{{ route('metrics.update') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(bodyData)
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success && !data.offline) console.error('Failed to update metric');
        });
    }

    // --- Existing: Item Quantity ---
    function changeQty(itemId, change) {
        let input = document.getElementById('qty_' + itemId);
        if (input.disabled) return;
        let newValue = (parseInt(input.value) || 1) + change;
        if (newValue >= parseInt(input.min || 1)) input.value = newValue;
    }

    // --- UPDATED: Item Toggle with SweetAlert ---
    function toggleItem(itemId, date) {
        let checkbox = document.getElementById('item_' + itemId);
        let qtyInput = document.getElementById('qty_' + itemId);
        let quantity = qtyInput.value;
        let isChecked = checkbox.checked;

        if (!isChecked) {
            // Undo visual check temporarily
            checkbox.checked = true; 
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to undo this? Your stock will be restored.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, undo it'
            }).then((result) => {
                if (result.isConfirmed) {
                    checkbox.checked = false; // Set to actual intent
                    executeItemToggle(itemId, date, quantity, false);
                }
            });
        } else {
            executeItemToggle(itemId, date, quantity, true);
        }
    }

    function executeItemToggle(itemId, date, quantity, isChecked) {
        let checkbox = document.getElementById('item_' + itemId);
        let checkIcon = document.querySelector('.check-icon-' + itemId);
        let stockCountSpan = document.getElementById('stock_count_' + itemId);
        let qtyInput = document.getElementById('qty_' + itemId);
        let btnDec = document.getElementById('btn_dec_' + itemId);
        let btnInc = document.getElementById('btn_inc_' + itemId);

        if(isChecked) checkIcon.classList.remove('hidden'); else checkIcon.classList.add('hidden');

        fetch('{{ route('log.toggle') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ item_id: itemId, date: date, quantity: quantity })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if(data.offline) {
                    showOfflineToast(data.message);
                    let parentDiv = checkbox.closest('.bg-white, .dark\\:bg-gray-800');
                    let currentStock = parseInt(stockCountSpan.innerText);
                    if (isChecked) {
                        parentDiv.classList.add('border-primary', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                        qtyInput.disabled = true; btnDec.disabled = true; btnInc.disabled = true;
                        stockCountSpan.innerText = currentStock - quantity; 
                    } else {
                        parentDiv.classList.remove('border-primary', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                        qtyInput.disabled = false; btnDec.disabled = false; btnInc.disabled = false;
                        stockCountSpan.innerText = currentStock + parseInt(quantity); 
                    }
                    return; 
                }

                stockCountSpan.innerText = data.current_stock;
                let parentDiv = checkbox.closest('.bg-white, .dark\\:bg-gray-800');
                if(data.status === 'checked') {
                    parentDiv.classList.add('border-primary', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                    qtyInput.disabled = true; btnDec.disabled = true; btnInc.disabled = true;
                } else {
                    parentDiv.classList.remove('border-primary', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                    qtyInput.disabled = false; btnDec.disabled = false; btnInc.disabled = false;
                }
                let progressPercent = (data.completed_count / data.total_count) * 100;
                document.getElementById('progress_bar').style.width = progressPercent + '%';
                document.getElementById('progress_text').innerText = data.completed_count + '/' + data.total_count + ' Done';
                setTimeout(() => window.location.reload(), 500); 
            } else {
                Swal.fire('Error', data.message, 'error');
                checkbox.checked = !isChecked;
                if(!isChecked) checkIcon.classList.remove('hidden'); else checkIcon.classList.add('hidden');
            }
        });
    }

    // --- UPDATED: Water Toggle with SweetAlert ---
    function toggleWater(btn, glassNumber) {
        let isFilled = btn.getAttribute('data-filled') === 'true';
        
        if (isFilled) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to unmark this glass of water?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6', // blue color
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unmark'
            }).then((result) => {
                if (result.isConfirmed) {
                    processWaterToggle('remove');
                }
            });
        } else {
            processWaterToggle('add');
        }
    }

    function processWaterToggle(action) {
        fetch('{{ route('water.toggle') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ action: action, date: selectedDate })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if(data.offline) {
                    showOfflineToast(data.message);
                    let currentText = document.getElementById('water_text').innerText;
                    let currentGlasses = parseInt(currentText.split('/')[0]);
                    let newGlasses = action === 'add' ? currentGlasses + 1 : currentGlasses - 1;
                    if(newGlasses > 8) newGlasses = 8;
                    if(newGlasses < 0) newGlasses = 0;
                    updateWaterUI(newGlasses);
                    return;
                }
                updateWaterUI(data.glasses);
            }
        });
    }

    function updateWaterUI(glassesCount) {
        document.getElementById('water_text').innerText = glassesCount + '/8';
        let buttons = document.getElementById('water-container').children;
        for(let i = 0; i < 8; i++) {
            let b = buttons[i];
            if(i < glassesCount) {
                b.setAttribute('data-filled', 'true');
                b.className = 'w-8 h-10 rounded-b-lg border-2 transition-all duration-300 bg-blue-400 border-blue-500 shadow-inner';
            } else {
                b.setAttribute('data-filled', 'false');
                b.className = 'w-8 h-10 rounded-b-lg border-2 transition-all duration-300 bg-white dark:bg-gray-700 border-blue-200 dark:border-blue-700';
            }
        }
    }
</script>
@endsection