<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuthController extends Controller
{
    // 1. Menampilkan Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Proses Login dengan Rate Limiting + Audit Log
    public function login(Request $request)
    {
        $loginValue = $request->input('username');
        $passwordValue = $request->input('password');

        // Validasi input tidak kosong
        if (!$loginValue || !$passwordValue) {
            return back()->withErrors([
                'username' => 'Kolom username/email dan password wajib diisi.',
            ])->withInput($request->only('username'));
        }

        // =====================================================
        // RATE LIMITING MANUAL MENGGUNAKAN SESSION
        // =====================================================
        $sessionKey = 'login_attempts_' . $request->ip();
        $attempts = session($sessionKey, 0);
        $lastAttemptTime = session($sessionKey . '_time', null);

        if ($attempts >= 5) {
            if ($lastAttemptTime) {
                $diff = now()->diffInSeconds($lastAttemptTime);
                if ($diff < 180) {
                    $remaining = 180 - $diff;
                    $minutes = ceil($remaining / 60);
                    return back()->withErrors([
                        'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
                    ])->withInput($request->only('username'));
                } else {
                    session([$sessionKey => 0]);
                    session([$sessionKey . '_time' => null]);
                }
            }
        }

        // Tentukan field (email atau username)
        $field = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $field => $loginValue,
            'password' => $passwordValue
        ];

        // Coba login
        if (Auth::attempt($credentials)) {
            // Login berhasil: reset session
            session([$sessionKey => 0]);
            session([$sessionKey . '_time' => null]);
            $request->session()->regenerate();

            // =====================================================
            // AUDIT LOG: Catat login berhasil
            // =====================================================
            AuditLog::create([
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'role' => Auth::user()->role,
                'action' => 'login',
                'description' => 'Login berhasil',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended('dashboard');
        }

        // Login gagal: tambah hit
        session([$sessionKey => $attempts + 1]);
        session([$sessionKey . '_time' => now()]);

        // Coba dengan field alternatif
        $alternativeField = ($field === 'email') ? 'username' : 'email';
        $alternativeCredentials = [
            $alternativeField => $loginValue,
            'password' => $passwordValue
        ];

        if (Auth::attempt($alternativeCredentials)) {
            session([$sessionKey => 0]);
            session([$sessionKey . '_time' => null]);
            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'role' => Auth::user()->role,
                'action' => 'login',
                'description' => 'Login berhasil (alternatif field)',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended('dashboard');
        }

        // =====================================================
        // AUDIT LOG: Catat login gagal (tanpa user_id karena belum login)
        // =====================================================
        AuditLog::create([
            'user_id' => null,
            'username' => $loginValue,
            'role' => null,
            'action' => 'login_failed',
            'description' => 'Percobaan login gagal',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withErrors([
            'username' => 'Username/email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('username'));
    }

    // 3. Proses Logout + Audit Log
    public function logout(Request $request)
    {
        // Audit log logout (hanya jika user masih login)
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'role' => Auth::user()->role,
                'action' => 'logout',
                'description' => 'Logout berhasil',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}