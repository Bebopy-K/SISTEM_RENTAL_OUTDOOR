<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Cabang;
use App\Models\Produk;
use App\Models\AuditLog;
use App\Models\EtlLog;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        // Hanya superadmin yang bisa mengakses
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengakses halaman ini.');
        }

        // =============================================
        // 1. STATISTIK USER
        // =============================================
        $totalUsers = User::count();
        $totalSuperadmin = User::where('role', 'superadmin')->count();
        $totalManager = User::where('role', 'manager')->count();
        $totalStaff = User::where('role', 'staff')->count();

        // User aktif (login dalam 15 menit terakhir berdasarkan updated_at)
        $activeUsers = User::where('updated_at', '>=', Carbon::now()->subMinutes(15))->count();

        // =============================================
        // 2. STATISTIK TRANSAKSI
        // =============================================
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $transaksiHariIni = Transaksi::whereDate('tanggal', $today)->count();
        $transaksiMingguIni = Transaksi::whereDate('tanggal', '>=', $weekStart)->count();
        $transaksiBulanIni = Transaksi::whereDate('tanggal', '>=', $monthStart)->count();
        $totalTransaksi = Transaksi::count();

        // Pendapatan hari ini
        $pendapatanHariIni = Transaksi::whereDate('tanggal', $today)->sum('total_harga') ?? 0;

        // =============================================
        // 3. DATA MASTER
        // =============================================
        $totalCabang = Cabang::count();
        $totalProduk = Produk::count();

        // =============================================
        // 4. STATUS KONEKSI DATABASE
        // =============================================
        try {
            DB::connection('mysql')->getPdo();
            $mysqlStatus = 'Connected';
            $mysqlInfo = DB::connection('mysql')->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Exception $e) {
            $mysqlStatus = 'Disconnected';
            $mysqlInfo = $e->getMessage();
        }

        try {
            DB::connection('dwh')->getPdo();
            $pgsqlStatus = 'Connected';
            $pgsqlInfo = DB::connection('dwh')->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Exception $e) {
            $pgsqlStatus = 'Disconnected';
            $pgsqlInfo = $e->getMessage();
        }

        // =============================================
        // 5. UKURAN DATABASE (MySQL)
        // =============================================
        $dbSize = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
                              FROM information_schema.tables 
                              WHERE table_schema = DATABASE()");
        $mysqlSize = $dbSize[0]->size_mb ?? 0;

        // =============================================
        // 6. AUDIT LOG TERBARU
        // =============================================
        $recentLogs = AuditLog::orderBy('created_at', 'desc')->limit(10)->get();

        // =============================================
        // 7. ETL TERAKHIR
        // =============================================
        $lastEtl = EtlLog::orderBy('finished_at', 'desc')->first();

        // =============================================
        // 8. GRAFIK TRANSAKSI 7 HARI TERAKHIR
        // =============================================
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $count = Transaksi::whereDate('tanggal', $date)->count();
            $chartData[] = $count;
        }

        // =============================================
        // 9. GRAFIK TRANSAKSI PER CABANG (Top 5)
        // =============================================
        $cabangTransaksi = DB::table('transaksi')
            ->join('cabang', 'transaksi.cabang_id', '=', 'cabang.id_cabang')
            ->select('cabang.nama_kota', DB::raw('COUNT(*) as total'))
            ->groupBy('cabang.nama_kota')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $cabangLabels = $cabangTransaksi->pluck('nama_kota');
        $cabangData = $cabangTransaksi->pluck('total');

        // =============================================
        // 10. SISTEM INFO
        // =============================================
        $phpVersion = phpversion();
        $laravelVersion = app()->version();
        $serverTime = Carbon::now()->format('Y-m-d H:i:s');

        return view('monitoring.index', compact(
            'totalUsers',
            'totalSuperadmin',
            'totalManager',
            'totalStaff',
            'activeUsers',
            'transaksiHariIni',
            'transaksiMingguIni',
            'transaksiBulanIni',
            'totalTransaksi',
            'pendapatanHariIni',
            'totalCabang',
            'totalProduk',
            'mysqlStatus',
            'mysqlInfo',
            'pgsqlStatus',
            'pgsqlInfo',
            'mysqlSize',
            'recentLogs',
            'lastEtl',
            'chartLabels',
            'chartData',
            'cabangLabels',
            'cabangData',
            'phpVersion',
            'laravelVersion',
            'serverTime'
        ));
    }
}