<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\TwoFactorCode;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Jika belum login atau sudah melewati 2FA, lanjutkan
        if (!Auth::check() || session('2fa_passed')) {
            return $next($request);
        }
        
        // LEWATI 2FA jika user tidak memiliki alamat email
        if (!$user->email) {
            session(['2fa_passed' => true]);
            return $next($request);
        }
        
        // Cek apakah sudah ada kode 2FA yang valid (belum kadaluarsa)
        $validCode = TwoFactorCode::where('user_id', $user->id_user)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->first();
        
        if (!$validCode) {
            // Generate kode acak 6 digit
            $code = rand(100000, 999999);
            
            // Simpan ke database
            TwoFactorCode::updateOrCreate(
                ['user_id' => $user->id_user, 'used' => false],
                [
                    'code' => $code,
                    'expires_at' => now()->addMinutes(10),
                ]
            );
            
            // Kirim email (pastikan user memiliki email)
            Mail::raw("Kode verifikasi 2FA Anda: $code", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode Verifikasi Dua Langkah');
            });
        }
        
        // Redirect ke halaman verifikasi 2FA
        return redirect()->route('2fa.verify');
    }
}