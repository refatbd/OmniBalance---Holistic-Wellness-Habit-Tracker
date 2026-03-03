<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetUserTimezone
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->timezone) {
            config(['app.timezone' => Auth::user()->timezone]);
            date_default_timezone_set(Auth::user()->timezone);
        }

        return $next($request);
    }
}