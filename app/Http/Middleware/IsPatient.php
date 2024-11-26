<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPatient
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guest()) {
            return redirect('/login'); // Arahkan ke login jika belum login
        }

        // Pastikan hanya role_id 4 (Patient) yang bisa lanjut ke reservasi
        if (auth()->user()->role_id !== 4) {
            return redirect('/'); // Arahkan ke home jika bukan Patient
        }

        return $next($request); // Lanjutkan jika role_id adalah 4
    }
}
