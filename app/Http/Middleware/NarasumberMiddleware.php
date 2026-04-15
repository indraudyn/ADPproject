<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NarasumberMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek user login & role narasumber
        if (Auth::check() && Auth::user()->role === 'narasumber') {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Hanya narasumber yang dapat mengakses halaman ini.');
    }
}
