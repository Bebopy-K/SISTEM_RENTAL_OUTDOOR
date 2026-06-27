<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Cabang;
use App\Models\EtlLog; // ← Tambahkan ini
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedBranch = $request->get('id_cabang');

        // 1. Cek Hak Akses (RBAC) berdasarkan kolom cabang_id di database
        $isAdminCabang = ($user->role === 'admin_cabang');
        $branchId = $isAdminCabang ? $user->cabang_id : $selectedBranch;

        // 2. Query data transaksi dasar
        $queryTransaksi = Transaksi::query();

        if ($isAdminCabang) {
            $queryTransaksi->where('cabang_id', $branchId);
            $daftarCabang = null;
        } else {
            $daftarCabang = Cabang::all();
            if ($branchId && $branchId !== 'all') {
                $queryTransaksi->where('cabang_id', $branchId);
            }
        }

        // 3. Hitung angka metrik utama
        $totalTransaksiCount = (clone $queryTransaksi)->count();
        $totalPendapatan = (clone $queryTransaksi)->sum('total_harga') ?? 0;
        $totalDenda = (clone $queryTransaksi)->sum('denda') ?? 0;
        $akumulasiFinansial = $totalPendapatan + $totalDenda;

        // 4. Siapkan data tren bulanan untuk grafik
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
                    $chartLabels[] = \Carbon\Carbon::parse($item->bulan . '-01')->format('F Y');
                    $chartData[] = (int) $item->total;
                }
            }
        }

        // 5. Ambil data ETL terakhir (hanya yang sukses)
        $lastEtl = EtlLog::where('status', 'success')
                         ->latest('finished_at')
                         ->first();

        // 6. Lempar ke view dashboard
        return view('dashboard', compact(
            'user',
            'daftarCabang',
            'selectedBranch',
            'totalTransaksiCount',
            'akumulasiFinansial',
            'chartLabels',
            'chartData',
            'lastEtl'  // ← tambahkan variabel ini
        ));
    }
}