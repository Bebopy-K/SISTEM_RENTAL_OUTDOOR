<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    /**
     * Menampilkan Daftar Pelanggan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        // Mengambil data pelanggan dengan fitur pencarian nama atau nomor HP
        $pelangganQuery = Pelanggan::query();

        if ($search) {
            $pelangganQuery->where('nama', 'LIKE', "%{$search}%")
                           ->orWhere('no_hp', 'LIKE', "%{$search}%");
        }

        // Semua role (Superadmin & Admin Cabang) bisa melihat data pelanggan global 
        // agar jika pelanggan menyewa di cabang berbeda, datanya tidak perlu didaftarkan ulang.
        $dataPelanggan = $pelangganQuery->orderBy('nama', 'asc')->paginate(10);

        return view('pelanggan.index', compact('user', 'dataPelanggan', 'search'));
    }

    /**
     * Form Pendaftaran Pelanggan Baru
     */
    public function create()
    {
        $user = Auth::user();
        return view('pelanggan.create', compact('user'));
    }

    /**
     * Menyimpan Data Pelanggan ke Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|unique:pelanggangs,nik', // Sesuaikan nama tabel database Anda jika ada typo
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        Pelanggan::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan baru berhasil diregistrasikan ke sistem pusat!');
    }
}