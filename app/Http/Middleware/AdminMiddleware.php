<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized - Please login'], 401);
        }
        
        if (!auth()->user()->is_admin) {
            return response()->json(['error' => 'Forbidden - Admin access required'], 403);
        }
        
        if (auth()->user()->status !== 'active') {
            return response()->json(['error' => 'Account is suspended'], 403);
        }
        
        return $next($request);
    }
}