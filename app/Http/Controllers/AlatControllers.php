<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alat; // Sesuaikan dengan nama model alat/produk Anda
use App\Models\Cabang;

class AlatController extends Controller
{
    /**
     * Menampilkan Daftar Stok Alat (Terfilter Otomatis Berdasarkan Cabang)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedBranch = $request->get('id_cabang');

        // Mengambil data alat beserta relasi cabangnya
        $alatQuery = Alat::with('cabang');

        // IMPLEMENTASI RBAC: Filter visualisasi stok barang
        if ($user->role === 'admin_cabang') {
            // Admin Cabang dipatok hanya bisa mengontrol stok di tokonya sendiri
            $alatQuery->where('id_cabang', $user->id_cabang);
            $daftarCabang = null;
        } else {
            // Superadmin bisa memantau pergerakan stok di seluruh 15 cabang
            $daftarCabang = Cabang::all();
            if ($selectedBranch && $selectedBranch !== 'all') {
                $alatQuery->where('id_cabang', $selectedBranch);
            }
        }

        $dataAlat = $alatQuery->orderBy('nama_alat', 'asc')->paginate(10);

        return view('alat.index', compact('user', 'dataAlat', 'daftarCabang', 'selectedBranch'));
    }

    /**
     * Form Tambah Stok / Alat Baru
     */
    public function create()
    {
        $user = Auth::user();

        // Hak input lokasi cabang saat penambahan barang
        if ($user->role === 'admin_cabang') {
            $daftarCabang = Cabang::where('id', $user->id_cabang)->get();
        } else {
            $daftarCabang = Cabang::all();
        }

        return view('alat.create', compact('user', 'daftarCabang'));
    }

    /**
     * Menyimpan Data Alat Baru ke Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori' => 'required|string',
            'id_cabang' => 'required',
            'harga_sewa_per_hari' => 'required|numeric',
            'stok_total' => 'required|numeric|min:0',
        ]);

        Alat::create([
            'nama_alat' => $request->nama_alat,
            'kategori' => $request->kategori,
            'id_cabang' => $request->id_cabang,
            'harga_sewa_per_hari' => $request->harga_sewa_per_hari,
            'stok_total' => $request->stok_total,
            'stok_tersedia' => $request->stok_total, // Default awal, stok tersedia sama dengan stok total
            'kondisi' => 'Bagus' // Status kondisi default awal barang baru masuk gudang
        ]);

        return redirect()->route('alat.index')->with('success', 'Data inventaris alat outdoor berhasil ditambahkan!');
    }
}