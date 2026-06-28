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
        // 1. Cek hak akses berdasarkan role
        // =====================================================
        if ($user->role === 'staff') {
            abort(403, 'Staff tidak memiliki akses ke halaman OLAP.');
        }

        // =====================================================
        // 2. Tentukan cabang yang akan ditampilkan
        // =====================================================
        $selectedCabang = $request->get('cabang'); // untuk superadmin

        if ($user->role === 'manager' || $user->role === 'admin_cabang') {
            // Manager/Admin cabang: hanya cabangnya
            $cabangId = $user->cabang_id;
            if (!$cabangId) {
                abort(403, 'Akun Anda tidak terhubung ke cabang tertentu.');
            }
            $cabangNama = $dwh->table('dim_cabang')
                ->where('id_cabang', $cabangId)
                ->value('nama_kota');
            if (!$cabangNama) {
                abort(403, 'Cabang tidak ditemukan di Data Warehouse.');
            }
            $selectedCabang = $cabangNama; // paksa filter
        }

        // =====================================================
        // 3. Query dasar (bisa difilter cabang)
        // =====================================================
        $query = $dwh->table('v_dashboard_nasional');
        if ($selectedCabang && $selectedCabang !== 'all') {
            $query->where('nama_kota', $selectedCabang);
        }

        // =====================================================
        // 4. Metrik utama
        // =====================================================
        $totalPendapatan = (clone $query)->sum('pendapatan_bersih');
        $totalTransaksi = (clone $query)->count();
        $rataPendapatan = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // =====================================================
        // 5. Grafik pendapatan per cabang (hanya superadmin)
        // =====================================================
        if ($user->role === 'superadmin' && (!$selectedCabang || $selectedCabang === 'all')) {
            $pendapatanPerCabang = $dwh->table('v_dashboard_nasional')
                ->select('nama_kota', $dwh->raw('SUM(pendapatan_bersih) as total'))
                ->groupBy('nama_kota')
                ->orderBy('total', 'desc')
                ->get();
        } else {
            $pendapatanPerCabang = collect();
        }

        // =====================================================
        // 6. Produk terlaris
        // =====================================================
        $produkTerlaris = (clone $query)
            ->select('nama_produk', $dwh->raw('SUM(jumlah_unit) as total_unit'))
            ->groupBy('nama_produk')
            ->orderBy('total_unit', 'desc')
            ->limit(5)
            ->get();

        // =====================================================
        // 7. Tren pendapatan harian (30 hari terakhir)
        // =====================================================
        $trenHarian = (clone $query)
            ->select('tanggal', $dwh->raw('SUM(pendapatan_bersih) as total'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->limit(30)
            ->get()
            ->reverse();

        // =====================================================
        // 8. Transaksi terbaru (20)
        // =====================================================
        $transaksiTerbaru = (clone $query)
            ->orderBy('tanggal', 'desc')
            ->limit(20)
            ->get();

        // =====================================================
        // 9. Data untuk filter cabang (hanya superadmin)
        // =====================================================
        $daftarCabang = [];
        if ($user->role === 'superadmin') {
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