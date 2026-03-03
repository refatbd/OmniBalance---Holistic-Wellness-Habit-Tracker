<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function toggleSuspend(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot suspend an admin.');
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $status = $user->is_suspended ? 'suspended' : 'activated';
        return back()->with('success', "User successfully {$status}.");
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete an admin.');
        }

        $user->delete(); // This will cascade delete their items and logs
        return back()->with('success', 'User deleted successfully.');
    }
}