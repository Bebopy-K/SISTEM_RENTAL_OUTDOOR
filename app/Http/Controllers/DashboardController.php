<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedBranch = $request->get('id_cabang', 'all');

        // =====================================================
        // 1. Tentukan cabang yang akan ditampilkan berdasarkan role
        // =====================================================
        $isSuperadmin = ($user->role === 'superadmin');
        $isManager = ($user->role === 'manager' || $user->role === 'admin_cabang'); // support legacy admin_cabang
        $isStaff = ($user->role === 'staff');

        // Query dasar untuk transaksi (dari MySQL/OLTP)
        $queryTransaksi = Transaksi::query();

        // Daftar cabang untuk filter (hanya untuk superadmin)
        $daftarCabang = null;

        if ($isSuperadmin) {
            // Superadmin: bisa melihat semua cabang atau filter per cabang
            $daftarCabang = Cabang::orderBy('nama_kota')->get();
            if ($selectedBranch !== 'all' && $selectedBranch) {
                $queryTransaksi->where('cabang_id', $selectedBranch);
            }
        } elseif ($isManager) {
            // Manager: hanya melihat cabangnya sendiri
            $cabangId = $user->cabang_id;
            if (!$cabangId) {
                abort(403, 'Akun manager tidak terhubung ke cabang tertentu.');
            }
            $queryTransaksi->where('cabang_id', $cabangId);
            // Filter hanya untuk tampilan, tidak perlu dropdown
            $selectedBranch = $cabangId;
        } elseif ($isStaff) {
            // Staff: hanya melihat cabangnya sendiri dan hanya data transaksi yang dia buat (opsional)
            // Jika ingin staff hanya melihat transaksi yang dia buat, tambahkan:
            // $queryTransaksi->where('user_id', $user->id_user);
            // Namun karena staff hanya boleh melihat data cabangnya, kita batasi ke cabang
            $cabangId = $user->cabang_id;
            if (!$cabangId) {
                abort(403, 'Akun staff tidak terhubung ke cabang tertentu.');
            }
            $queryTransaksi->where('cabang_id', $cabangId);
            $selectedBranch = $cabangId;
        } else {
            // Role tidak dikenal
            abort(403, 'Anda tidak memiliki akses ke dashboard.');
        }

        // =====================================================
        // 2. Hitung metrik utama
        // =====================================================
        $totalTransaksiCount = (clone $queryTransaksi)->count();
        $totalPendapatan = (clone $queryTransaksi)->sum('total_harga') ?? 0;
        $totalDenda = (clone $queryTransaksi)->sum('denda') ?? 0;
        $akumulasiFinansial = $totalPendapatan + $totalDenda;

        // =====================================================
        // 3. Data untuk grafik tren bulanan (dari transaksi yang sudah difilter)
        // =====================================================
        $trenBulanan = (clone $queryTransaksi)
            ->select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"), 
                DB::raw('SUM(total_harga) as total')
            )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        $chartLabels = [];
        $chartData = [];

        if ($trenBulanan->isNotEmpty()) {
            foreach ($trenBulanan as $item) {
                if ($item->bulan) {
                    $chartLabels[] = \Carbon\Carbon::parse($item->bulan . '-01')->translatedFormat('F Y');
                    $chartData[] = (int) $item->total;
                }
            }
        }

        // =====================================================
        // 4. Kirim ke view
        // =====================================================
        return view('dashboard', compact(
            'user', 
            'daftarCabang', 
            'selectedBranch', 
            'totalTransaksiCount', 
            'akumulasiFinansial', 
            'chartLabels',
            'chartData'
        ));
    }
}