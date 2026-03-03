<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } elseif (auth()->check() && auth()->user()->language) {
            // If user is logged in and has a language preference, use it
            App::setLocale(auth()->user()->language);
            Session::put('locale', auth()->user()->language);
        }

        return $next($request);
    }
}