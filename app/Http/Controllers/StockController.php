<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function update(Request $request, $id)
    {
        $request->validate([
            'stock_adjustment' => 'required|integer|min:1',
            'action' => 'required|in:add,subtract,set' // Added 'subtract'
        ]);

        $item = Item::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Handle Add, Subtract, or Set actions
        if ($request->action === 'add') {
            $item->stock += $request->stock_adjustment;
        } elseif ($request->action === 'subtract') {
            $item->stock -= $request->stock_adjustment;
        } else {
            $item->stock = $request->stock_adjustment;
        }

        // Prevent negative stock
        if ($item->stock < 0) {
            $item->stock = 0;
        }

        $item->save();

        return redirect()->route('items.index')->with('success', 'Stock updated successfully!');
    }
}