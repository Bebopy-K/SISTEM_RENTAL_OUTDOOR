<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Menampilkan Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Proses Login dengan Rate Limiting Manual (Session)
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

        // Jika sudah 5 kali percobaan
        if ($attempts >= 5) {
            // Cek apakah masih dalam masa jeda (3 menit)
            if ($lastAttemptTime) {
                $diff = now()->diffInSeconds($lastAttemptTime);
                if ($diff < 180) {
                    $remaining = 180 - $diff;
                    $minutes = ceil($remaining / 60);
                    return back()->withErrors([
                        'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
                    ])->withInput($request->only('username'));
                } else {
                    // Reset jika sudah lebih dari 3 menit
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
            return redirect()->intended('dashboard');
        }

        // Login gagal: tambah hit
        session([$sessionKey => $attempts + 1]);
        session([$sessionKey . '_time' => now()]);

        // Coba dengan field alternatif (jika input email, coba username; dan sebaliknya)
        $alternativeField = ($field === 'email') ? 'username' : 'email';
        $alternativeCredentials = [
            $alternativeField => $loginValue,
            'password' => $passwordValue
        ];

        if (Auth::attempt($alternativeCredentials)) {
            // Login berhasil dengan field alternatif
            session([$sessionKey => 0]);
            session([$sessionKey . '_time' => null]);
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // Jika semua gagal, kembalikan error
        return back()->withErrors([
            'username' => 'Username/email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('username'));
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}