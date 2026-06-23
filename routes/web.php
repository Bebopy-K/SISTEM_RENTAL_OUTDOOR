<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OlapController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\EtlController;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes - Outdoor Rental System
|--------------------------------------------------------------------------
*/

// Halaman awal -> redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// RUTE LOGIN DENGAN GOOGLE
// ==========================================
Route::get('login/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

// ==========================================
// RUTE LUPA SANDI (FORGOT PASSWORD)
// ==========================================
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/reset-password/{token}', function (Request $request, $token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->query('email')
    ]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );
    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->name('password.update');

// ==========================================
// RUTE AUTENTIKASI (GUEST) + RATE LIMITING
// ==========================================
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.proses')
        ->middleware('login.limiter');
});

// ==========================================
// RUTE 2FA (WAJIB LOGIN, TAPI BELUM DIPERIKSA 2FA)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.submit');
    Route::post('/2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');
});

// ==========================================
// RUTE YANG WAJIB LOGIN DAN TELAH LOLOS 2FA
// ==========================================
Route::middleware(['auth', 'twofactor'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('transaksi', TransaksiController::class)->except(['show']);
    Route::get('/olap', [OlapController::class, 'index'])->name('olap');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ==========================================
// RUTE ETL (KHUSUS SUPERADMIN)
// ==========================================
Route::middleware(['auth', 'twofactor', 'role:superadmin'])->group(function () {
    Route::post('/etl/sync', [EtlController::class, 'sync'])->name('etl.sync');
});

// Pengalihan /home ke dashboard (tidak perlu 2FA karena hanya redirect)
Route::get('/home', function () {
    return redirect()->route('dashboard');
});

// ==========================================
// RUTE TESTING (HAPUS SAAT PRODUKSI)
// ==========================================
Route::get('/test-login', function() {
    $emailYangDicari = 'siicantik404@gmail.com';
    $passwordBaruAnda = 'jagung9145';

    $user = User::where('email', $emailYangDicari)->first();
    if (!$user) {
        return "User tidak ditemukan!";
    }

    $isPasswordMatch = Hash::check($passwordBaruAnda, $user->password);
    return [
        'Email' => $emailYangDicari,
        'Username' => $user->username,
        'Password cocok?' => $isPasswordMatch ? 'YA' : 'TIDAK',
        'Rekomendasi' => $isPasswordMatch
            ? "Login dengan username '{$user->username}' dan password baru Anda."
            : "Password tidak cocok. Coba reset ulang."
    ];
});
