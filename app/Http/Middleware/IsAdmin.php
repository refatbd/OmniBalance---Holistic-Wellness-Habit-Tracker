<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }
        
        // If not admin, throw a 403 Forbidden error or redirect
        abort(403, 'Unauthorized access. Admins only.');
    }
}