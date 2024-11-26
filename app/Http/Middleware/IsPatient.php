<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPatient
{
    public function handle($request, Closure $next)
    {
        $patient = Auth::guard('patient')->user();

        if (!$patient) {
            // Redirect jika tidak memiliki akses pasien
            return redirect()->route('patient.login')->with('error', 'Unauthorized access.');
        }

        // Jika memiliki akses, lanjutkan request
        return $next($request);
    }
}
