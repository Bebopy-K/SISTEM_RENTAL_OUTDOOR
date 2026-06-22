<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class LoginRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kunci berdasarkan IP pengguna
        $key = 'login-attempts:' . $request->ip();

        // Jika sudah mencapai batas (5 kali gagal)
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
            ])->withInput($request->only('username'));
        }

        // Proses login (eksekusi controller)
        $response = $next($request);

        // Jika login gagal (status 422 atau 401), tambahkan hit ke rate limiter
        if ($response->status() === 422 || $response->status() === 401) {
            RateLimiter::hit($key, 180); // 180 detik = 3 menit
        } else {
            // Jika login berhasil, reset hit
            RateLimiter::clear($key);
        }

        return $response;
    }
}