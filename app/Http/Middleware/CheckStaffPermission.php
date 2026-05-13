<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStaffPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Admin ko koi restriction nahi
        if (auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Staff ki permissions check karo
        $perms = \App\Models\StaffPermission::where('user_id', auth()->id())
            ->first();

        if (!$perms || !$perms->$permission) {
            return redirect()->route('sales.pos')
                ->with('error', '❌ Aapke paas yeh page access karne ki permission nahi!');
        }

        return $next($request);
    }
}