<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsInternal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guest()) {
            // abort(403);
            return redirect('/');
        }

        $allowedRoles = [1, 2, 3];
        if (!in_array(auth()->user()->role_id, $allowedRoles)) {
            // abort(403);
            return redirect('/');
        }

        return $next($request);
    }
}
