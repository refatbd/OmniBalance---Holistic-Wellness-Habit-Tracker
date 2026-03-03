<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // If user is already logged in, send them to their dashboard
        if (auth()->check()) {
            return auth()->user()->isAdmin() 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('dashboard');
        }

        return view('welcome');
    }
}