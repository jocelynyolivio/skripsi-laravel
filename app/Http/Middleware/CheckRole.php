<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $roleMap = [
            1 => 'admin',
            2 => 'dokter_tetap',
            3 => 'dokter_luar',
            4 => 'manager',
        ];

        $userRoleName = $roleMap[$user->role_id] ?? null;

        if (!$userRoleName || !in_array($userRoleName, $roles)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
