<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Sirf Admin access kar sakta hai!');
        }
        return $next($request);
    }
}