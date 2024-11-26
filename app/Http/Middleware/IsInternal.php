<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsInternal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Periksa apakah pengguna internal memiliki role
        if (!$user || !$user->role) {
            return redirect('/login')->with('error', 'Access Denied.');
        }

        // Lanjutkan jika role valid
        return $next($request);
    }
}
