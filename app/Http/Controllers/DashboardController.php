<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Cabang;
use App\Models\EtlLog; // <-- Tambahkan ini
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
        $isManager = ($user->role === 'manager' || $user->role === 'admin_cabang');
        $isStaff = ($user->role === 'staff');

        $queryTransaksi = Transaksi::query();
        $daftarCabang = null;

        if ($isSuperadmin) {
            $daftarCabang = Cabang::orderBy('nama_kota')->get();
            if ($selectedBranch !== 'all' && $selectedBranch) {
                $queryTransaksi->where('cabang_id', $selectedBranch);
            }
        } elseif ($isManager) {
            $cabangId = $user->cabang_id;
            if (!$cabangId) {
                abort(403, 'Akun manager tidak terhubung ke cabang tertentu.');
            }
            $queryTransaksi->where('cabang_id', $cabangId);
            $selectedBranch = $cabangId;
        } elseif ($isStaff) {
            $cabangId = $user->cabang_id;
            if (!$cabangId) {
                abort(403, 'Akun staff tidak terhubung ke cabang tertentu.');
            }
            $queryTransaksi->where('cabang_id', $cabangId);
            $selectedBranch = $cabangId;
        } else {
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
        // 3. Data grafik tren bulanan
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
        // 4. Ambil data ETL terakhir (hanya untuk superadmin)
        // =====================================================
        $lastEtl = null;
        if ($isSuperadmin) {
            $lastEtl = EtlLog::orderBy('finished_at', 'desc')->first();
        }

        // =====================================================
        // 5. Kirim ke view
        // =====================================================
        return view('dashboard', compact(
            'user', 
            'daftarCabang', 
            'selectedBranch', 
            'totalTransaksiCount', 
            'akumulasiFinansial', 
            'chartLabels',
            'chartData',
            'lastEtl' // <-- Tambahkan ini
        ));
    }
}