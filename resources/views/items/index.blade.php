@extends('layouts.app')

@section('content')
<div class="p-4 pb-24">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ __('messages.inventory') }}</h2>
        <button onclick="openModal('addModal')" class="bg-primary hover:bg-emerald-600 text-white p-2 rounded-full shadow-md transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 p-3 rounded-xl mb-4 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @foreach($items as $item)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-3 transition-opacity {{ !$item->is_active ? 'opacity-60 grayscale-[50%]' : '' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $item->icon }} {{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $item->instruction }}</p>
                        <div class="flex gap-2 mt-2 flex-wrap">
                            <span class="text-[10px] bg-purple-50 dark:bg-purple-900/20 px-1.5 py-0.5 rounded text-purple-600 dark:text-purple-400">{{ $item->category }}</span>
                            
                            @if(!$item->is_active)
                                <span class="text-[10px] bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded text-red-600 dark:text-red-400 font-bold">Disabled</span>
                            @endif
                            
                            <span class="text-[10px] bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500">Cal: {{ number_format($item->calories, 0) }}</span>
                            <span class="text-[10px] bg-blue-50 dark:bg-blue-900/20 px-1.5 py-0.5 rounded text-blue-500">P: {{ number_format($item->protein, 1) }}g</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-xl font-bold {{ $item->stock <= $item->low_stock_threshold ? 'text-red-500' : 'text-primary' }}">
                            {{ $item->stock }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item->unit }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700 mt-1">
                    <div class="flex space-x-2">
                        <form action="{{ route('stock.update', $item->id) }}" method="POST" class="flex items-center gap-2 flex-wrap">
                            @csrf
                            
                            <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-md overflow-hidden bg-white dark:bg-gray-700">
                                <button type="button" onclick="changeInventoryQty({{ $item->id }}, -1)" class="w-7 h-7 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">-</button>
                                
                                <input type="number" id="inv_qty_{{ $item->id }}" name="stock_adjustment" value="1" min="1" required 
                                       class="w-10 h-7 px-1 text-xs text-center border-none focus:ring-0 dark:bg-gray-700 dark:text-white [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none m-0"
                                       style="-moz-appearance: textfield;">
                                       
                                <button type="button" onclick="changeInventoryQty({{ $item->id }}, 1)" class="w-7 h-7 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">+</button>
                            </div>

                            <button type="submit" name="action" value="add" class="bg-emerald-100 dark:bg-emerald-900/30 hover:bg-emerald-200 dark:hover:bg-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-xs px-3 py-1.5 rounded-lg font-bold transition-colors">
                                Add
                            </button>
                            <button type="submit" name="action" value="subtract" class="bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 dark:hover:bg-orange-800/50 text-orange-700 dark:text-orange-400 text-xs px-3 py-1.5 rounded-lg font-bold transition-colors">
                                Reduce
                            </button>
                        </form>
                    </div>
                    
                    <div class="flex items-center gap-2 ml-2">
                        <form action="{{ route('items.toggle', $item->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-bold transition-colors {{ $item->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                                {{ $item->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>

                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="confirmDelete(event, this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-600 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[60] flex items-end justify-center sm:items-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md overflow-hidden transform transition-all max-h-[90vh] flex flex-col">
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.add_new') }}</h3>
            <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('items.store') }}" method="POST" class="p-4 space-y-4 overflow-y-auto">
            @csrf
            <div class="flex gap-4">
                <div class="w-1/4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.icon') }}</label>
                    <input type="text" name="icon" placeholder="🍎" class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
                <div class="w-3/4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.name') }}</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <select name="category" required class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                    <option value="Supplements">Supplements</option>
                    <option value="Meals">Meals</option>
                    <option value="Hydration">Hydration</option>
                    <option value="Medication">Medication</option>
                    <option value="General">General</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.instruction') }}</label>
                <input type="text" name="instruction" placeholder="e.g. Soak in water for 20 mins" class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.timing') }}</label>
                <input type="text" name="timing" placeholder="e.g. Morning, Empty stomach" class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Calories (per unit)</label>
                    <input type="number" name="calories" step="0.01" value="0" class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Protein (g)</label>
                    <input type="number" name="protein" step="0.01" value="0" class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Carbs (g)</label>
                    <input type="number" name="carbs" step="0.01" value="0" class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fats (g)</label>
                    <input type="number" name="fats" step="0.01" value="0" class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.initial_stock') }}</label>
                    <input type="number" name="stock" value="0" min="0" required class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
                <div class="w-1/2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.unit') }}</label>
                    <input type="text" name="unit" placeholder="Pcs, Spoon, Gm" required class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Low Stock Alert At</label>
                <input type="number" name="low_stock_threshold" value="5" min="0" required class="w-full px-3 py-2 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                <p class="text-[10px] text-gray-500 mt-1">Receive a notification when your stock drops below this number.</p>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-3 rounded-xl transition-colors mt-4 shrink-0">
                {{ __('messages.save_item') }}
            </button>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    
    function changeInventoryQty(itemId, change) {
        let input = document.getElementById('inv_qty_' + itemId);
        let currentValue = parseInt(input.value) || 1;
        let newValue = currentValue + change;
        
        if (newValue >= 1) {
            input.value = newValue;
        }
    }

    // NEW: SweetAlert function for delete confirmation
    function confirmDelete(event, form) {
        event.preventDefault(); // Stop immediate form submission
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to delete this item? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // red-500
            cancelButtonColor: '#6b7280', // gray-500
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit the form if confirmed
            }
        });
    }
</script>
@endsection