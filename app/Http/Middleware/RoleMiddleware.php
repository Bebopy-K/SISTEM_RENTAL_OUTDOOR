<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika user belum login, arahkan ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role;

        // Jika tidak ada role yang diizinkan (tidak wajib, tapi kita tetapkan)
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah user memiliki salah satu role yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki akses, tampilkan error 403
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}