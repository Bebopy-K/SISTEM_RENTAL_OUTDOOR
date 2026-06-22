<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\TwoFactorCode;

class TwoFactorController extends Controller
{
    public function showVerifyForm()
    {
        return view('auth.2fa-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        $now = now(); // menggunakan helper now() Laravel (sudah sesuai timezone)

        // Cari kode yang valid
        $validCode = TwoFactorCode::where('user_id', $user->id_user)
            ->where('code', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', $now)
            ->first();

        if ($validCode) {
            $validCode->update(['used' => true]);
            session(['2fa_passed' => true]);
            return redirect()->intended('/dashboard');
        }

        // Debug: catat kode yang dimasukkan vs kode terakhir di database
        $lastCode = TwoFactorCode::where('user_id', $user->id_user)->latest()->first();
        \Log::info('Gagal verifikasi 2FA', [
            'user' => $user->username,
            'input_code' => $request->code,
            'last_code_in_db' => $lastCode ? $lastCode->code : 'tidak ada',
            'expires_at' => $lastCode ? $lastCode->expires_at : null,
            'now' => $now,
        ]);

        return back()->withErrors(['code' => 'Kode 2FA salah atau sudah kadaluarsa.']);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        // Hapus semua kode lama yang belum digunakan
        TwoFactorCode::where('user_id', $user->id_user)->where('used', false)->delete();

        $code = rand(100000, 999999);
        TwoFactorCode::create([
            'user_id' => $user->id_user,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'used' => false,
        ]);

        // Kirim email atau log
        if ($user->email) {
            Mail::raw("Kode verifikasi 2FA Anda: $code", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode Verifikasi Dua Langkah (Kirim Ulang)');
            });
        } else {
            \Log::info('2FA Code untuk ' . $user->username . ': ' . $code);
        }

        return back()->with('status', 'Kode verifikasi baru telah dikirim (masa berlaku 10 menit).');
    }
}