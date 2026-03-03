<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Auth::user()->activities()->orderBy('created_at', 'desc')->get();
        return view('activities.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'default_duration' => 'required|integer|min:1',
        ]);

        Auth::user()->activities()->create($validated);

        return redirect()->route('activities.index')->with('success', 'Activity added successfully!');
    }

    public function update(Request $request, $id)
    {
        $activity = UserActivity::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'default_duration' => 'required|integer|min:1',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.index')->with('success', 'Activity updated successfully!');
    }

    public function destroy($id)
    {
        $activity = UserActivity::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Activity removed successfully!');
    }
}