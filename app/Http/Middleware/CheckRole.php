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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Admin dapat mengakses semua halaman
        if ($request->user()->role === 'admin') {
            return $next($request);
        }

        // Cek apakah role user ada dalam daftar role yang diizinkan
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}