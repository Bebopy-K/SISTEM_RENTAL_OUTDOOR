<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OlapController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // CHANGED: Remove 'dwh' so it automatically uses the default PostgreSQL connection from your .env
        $dwh = DB::connection(); 

        // =====================================================
        // 1. Tentukan cabang yang akan ditampilkan
        // =====================================================
        $selectedCabang = $request->get('cabang'); 

        if ($user->role == 'admin_cabang') {
            $cabangId = $user->cabang_id; 
            if (!$cabangId) {
                abort(403, 'Akun admin cabang tidak memiliki cabang yang valid.');
            }
            $cabangNama = $dwh->table('dim_cabang')
                ->where('id_cabang', $cabangId)
                ->value('nama_kota');
            if (!$cabangNama) {
                abort(403, 'Cabang tidak ditemukan di Data Warehouse.');
            }
            $selectedCabang = $cabangNama; 
        }

        // =====================================================
        // 2. Query dasar (bisa difilter cabang)
        // =====================================================
        $query = $dwh->table('v_dashboard_nasional');
        if ($selectedCabang && $selectedCabang != 'all') {
            $query->where('nama_kota', $selectedCabang);
        }

        // =====================================================
        // 3. Metrik utama
        // =====================================================
        $totalPendapatan = (clone $query)->sum('pendapatan_bersih');
        $totalTransaksi = (clone $query)->count();
        $rataPendapatan = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // =====================================================
        // 4. Grafik pendapatan per cabang
        // =====================================================
        if ($user->role == 'superadmin' && (!$selectedCabang || $selectedCabang == 'all')) {
            $pendapatanPerCabang = $dwh->table('v_dashboard_nasional')
                ->select('nama_kota', DB::raw('SUM(pendapatan_bersih) as total'))
                ->groupBy('nama_kota')
                ->orderBy('total', 'desc')
                ->get();
        } else {
            $pendapatanPerCabang = collect(); 
        }

        // =====================================================
        // 5. Produk terlaris
        // =====================================================
        $produkTerlaris = (clone $query)
            ->select('nama_produk', DB::raw('SUM(jumlah_unit) as total_unit'))
            ->groupBy('nama_produk')
            ->orderBy('total_unit', 'desc')
            ->limit(5)
            ->get();

        // =====================================================
        // 6. Tren pendapatan harian (30 hari terakhir)
        // =====================================================
        $trenHarian = (clone $query)
            ->select('tanggal', DB::raw('SUM(pendapatan_bersih) as total'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->limit(30)
            ->get()
            ->reverse();

        // =====================================================
        // 7. Transaksi terbaru (20)
        // =====================================================
        $transaksiTerbaru = (clone $query)
            ->orderBy('tanggal', 'desc')
            ->limit(20)
            ->get();

        // =====================================================
        // 8. Data untuk filter cabang
        // =====================================================
        $daftarCabang = [];
        if ($user->role == 'superadmin') {
            $daftarCabang = $dwh->table('dim_cabang')
                ->select('nama_kota')
                ->orderBy('nama_kota')
                ->pluck('nama_kota')
                ->toArray();
        }

        return view('olap.index', compact(
            'totalPendapatan',
            'totalTransaksi',
            'rataPendapatan',
            'pendapatanPerCabang',
            'produkTerlaris',
            'trenHarian',
            'transaksiTerbaru',
            'selectedCabang',
            'daftarCabang'
        ));
    }
}