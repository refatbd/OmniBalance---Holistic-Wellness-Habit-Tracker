<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ConsumptionLog;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalLogs = ConsumptionLog::count();
        
        return view('admin.dashboard', compact('totalUsers', 'totalAdmins', 'totalLogs'));
    }
}