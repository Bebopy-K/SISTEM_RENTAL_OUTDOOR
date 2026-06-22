<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['login' => 'Login Google gagal. Silakan coba lagi.']);
        }

        // 1. Cek user berdasarkan google_id
        $user = User::where('google_id', $googleUser->id)->first();

        // 2. Jika tidak ditemukan, cek berdasarkan email
        if (!$user) {
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // User sudah ada (via form biasa), update google_id
                $user->google_id = $googleUser->id;
                $user->username = $googleUser->email; // bisa disesuaikan
                $user->save();
            } else {
                // 3. Buat user baru
                $user = User::create([
                    'username' => $googleUser->email,
                    'email' => $googleUser->email,
                    'password' => bcrypt(Str::random(24)),
                    'role' => 'admin_cabang', // atau role lain yang sesuai
                    'google_id' => $googleUser->id,
                ]);
            }
        }

        Auth::login($user, true);
        session()->forget('2fa_passed'); // reset 2FA session

        return redirect()->intended('/dashboard');
    }
}