<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $items = Auth::user()->items()->orderBy('created_at', 'desc')->get();
        return view('items.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100', // Added validation for category
            'instruction' => 'nullable|string|max:255',
            'timing' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            // Added validation for macro-nutrients
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            // --- NEW: Added validation for low stock threshold ---
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        Auth::user()->items()->create($validated);

        return redirect()->route('items.index')->with('success', 'Item added successfully!');
    }

    public function update(Request $request, $id)
    {
        $item = Item::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100', // Added validation for category
            'instruction' => 'nullable|string|max:255',
            'timing' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            // Added validation for macro-nutrients
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            // --- NEW: Added validation for low stock threshold ---
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item updated successfully!');
    }

    public function destroy($id)
    {
        $item = Item::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item removed successfully!');
    }

    // --- NEW: Toggle Active Status ---
    public function toggleActive($id)
    {
        $item = Item::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $item->is_active = !$item->is_active;
        $item->save();

        $status = $item->is_active ? 'enabled' : 'disabled';
        return redirect()->route('items.index')->with('success', "Item {$status} successfully!");
    }
}