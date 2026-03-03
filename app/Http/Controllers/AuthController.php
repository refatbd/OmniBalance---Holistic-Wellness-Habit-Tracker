<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\DefaultItemSeeder;
use Database\Seeders\DefaultActivitySeeder; // NEW IMPORT ADDED HERE

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Check if user is suspended
            if (Auth::user()->is_suspended) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your account has been suspended by the administrator.']);
            }

            $request->session()->regenerate();
            
            // Redirect admin to admin dashboard, normal user to user dashboard
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    // New Registration Methods
    public function showRegistrationForm()
    {
        // Check if admin disabled registration
        if (Setting::get('enable_registration', '1') !== '1') {
            abort(403, 'Registration is currently disabled.');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (Setting::get('enable_registration', '1') !== '1') {
            abort(403, 'Registration is currently disabled.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'timezone' => 'required|string|timezone', // Validate that it's a valid PHP timezone
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'language' => session('locale', 'bn'),
            'timezone' => $request->timezone // Save the user's selected timezone
        ]);

        // 1. Inject default items (Food/Supplements) for the new user!
        $defaultItems = DefaultItemSeeder::getDefaultItems();
        foreach ($defaultItems as $item) {
            $user->items()->create(array_merge($item, ['stock' => 50]));
        }

        // 2. NEW: Inject default Activities/Habits (Exercise/Meditation)
        $defaultActivities = DefaultActivitySeeder::getDefaultActivities();
        foreach ($defaultActivities as $activity) {
            $user->activities()->create($activity);
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Welcome! We have added some default items and activities to get you started.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); // Redirect to welcome page instead of login
    }
}