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
        $dwh = DB::connection('dwh');

        // =====================================================
        // 1. Tentukan cabang yang akan ditampilkan
        // =====================================================
        $selectedCabang = $request->get('cabang'); // untuk superadmin

        if ($user->role == 'admin_cabang') {
            // Admin cabang: ambil nama kota dari cabang tugasnya (berdasarkan cabang_id di tabel users MySQL)
            // Karena cabang_id di MySQL harus sama dengan id_cabang di PostgreSQL
            $cabangId = $user->cabang_id; // dari tabel users (MySQL)
            if (!$cabangId) {
                abort(403, 'Akun admin cabang tidak memiliki cabang yang valid.');
            }
            $cabangNama = $dwh->table('dim_cabang')
                ->where('id_cabang', $cabangId)
                ->value('nama_kota');
            if (!$cabangNama) {
                abort(403, 'Cabang tidak ditemukan di Data Warehouse.');
            }
            $selectedCabang = $cabangNama; // paksa hanya cabang ini
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
                ->select('nama_kota', $dwh->raw('SUM(pendapatan_bersih) as total')) // DIUBAH: DB::raw -> $dwh->raw
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
            ->select('nama_produk', $dwh->raw('SUM(jumlah_unit) as total_unit')) // DIUBAH: DB::raw -> $dwh->raw
            ->groupBy('nama_produk')
            ->orderBy('total_unit', 'desc')
            ->limit(5)
            ->get();

        // =====================================================
        // 6. Tren pendapatan harian (30 hari terakhir)
        // =====================================================
        $trenHarian = (clone $query)
            ->select('tanggal', $dwh->raw('SUM(pendapatan_bersih) as total')) // DIUBAH: DB::raw -> $dwh->raw
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
        // 8. Data untuk filter cabang (hanya untuk superadmin)
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
