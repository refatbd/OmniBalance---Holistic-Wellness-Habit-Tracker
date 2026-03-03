<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user profile settings page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'timezone' => ['required', 'string', 'timezone'],
            'language' => ['required', 'in:en,bn'],
            'water_reminder_interval' => ['nullable', 'integer', 'in:30,60,120'], // Added validation
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->timezone = $validated['timezone'];
        $user->language = $validated['language'];
        
        // Update reminder interval preference
        $user->water_reminder_interval = $validated['water_reminder_interval']; 
        
        // Save the bedtime notification preference (if it's missing from the request, it means unchecked/false)
        $user->receive_bedtime_notifications = $request->has('receive_bedtime_notifications');

        // Update language in session to reflect change immediately
        session()->put('locale', $validated['language']);

        // Handle password update if provided
        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Send a test push notification to the user.
     */
    public function testNotification(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has an active push subscription
        if ($user->pushSubscriptions()->count() > 0) {
            $user->notify(new \App\Notifications\DailyReminder());
            return response()->json(['success' => true, 'message' => 'Test notification sent! Check your device.']);
        }
        
        return response()->json(['success' => false, 'message' => 'No push subscription found. Please make sure you have allowed notifications for this site.']);
    }
}