<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  ...$roles  // Menerima satu atau lebih nama peran
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika pengguna tidak login, biarkan middleware 'auth' yang menanganinya
        if (!Auth::check()) {
            return $next($request);
        }

        // Periksa setiap peran yang diizinkan
        foreach ($roles as $role) {
            // Jika pengguna memiliki salah satu peran yang diizinkan, lanjutkan permintaan
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Jika pengguna tidak memiliki peran yang diizinkan, tolak akses
        abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES.');
    }
}
