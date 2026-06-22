<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sewa;
use App\Models\Cabang;
use App\Models\Pelanggan;
use App\Models\Alat; // Pastikan model Alat di-import
use Carbon\Carbon;

class SewaController extends Controller
{
    // ... fungsi index() yang sudah kita buat sebelumnya tetap sama ...
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedBranch = $request->get('id_cabang');
        $sewaQuery = Sewa::with(['cabang', 'pelanggan', 'user']);

        if ($user->role === 'admin_cabang') {
            $sewaQuery->where('id_cabang', $user->id_cabang);
            $daftarCabang = null;
        } else {
            $daftarCabang = Cabang::all();
            if ($selectedBranch && $selectedBranch !== 'all') {
                $sewaQuery->where('id_cabang', $selectedBranch);
            }
        }

        $dataSewa = $sewaQuery->orderBy('tgl_sewa', 'desc')->paginate(10);
        return view('sewa.index', compact('user', 'dataSewa', 'daftarCabang', 'selectedBranch'));
    }

    /**
     * FORM TRANSAKSI BARU
     */
    public function create()
    {
        $user = Auth::user();
        $daftarPelanggan = Pelanggan::orderBy('nama', 'asc')->get();
        
        // Admin hanya bisa melayani sewa di cabangnya, Superadmin bisa di semua cabang
        if ($user->role === 'admin_cabang') {
            $daftarCabang = Cabang::where('id', $user->id_cabang)->get();
            $daftarAlat = Alat::where('id_cabang', $user->id_cabang)->where('stok_tersedia', '>', 0)->get();
        } else {
            $daftarCabang = Cabang::all();
            $daftarAlat = Alat::where('stok_tersedia', '>', 0)->get();
        }

        return view('sewa.create', compact('user', 'daftarPelanggan', 'daftarCabang', 'daftarAlat'));
    }

    /**
     * SIMPAN TRANSAKSI BARU & POTONG STOK
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required',
            'id_cabang' => 'required',
            'id_alat' => 'required', // Menangkap alat yang dipilih
            'tgl_sewa' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_sewa',
        ]);

        // 1. Ambil data alat untuk tahu harga sewa per hari
        $alat = Alat::findOrFail($request->id_alat);

        // 2. Hitung durasi hari sewa
        $tglSewa = Carbon::parse($request->tgl_sewa);
        $tglKembali = Carbon::parse($request->tgl_kembali);
        $durasi = $tglSewa->diffInDays($tglKembali);
        if ($durasi == 0) $durasi = 1; // Minimal sewa 1 hari

        // 3. Hitung total harga otomatis di backend (lebih aman)
        $totalHarga = $durasi * $alat->harga_sewa_per_hari;

        // 4. Simpan Transaksi Sewa
        Sewa::create([
            'id_pelanggan' => $request->id_pelanggan,
            'id_cabang' => $request->id_cabang,
            'id_user' => Auth::id(),
            'tgl_sewa' => $request->tgl_sewa,
            'tgl_kembali' => $request->tgl_kembali,
            'total_harga' => $totalHarga,
            'status' => 'Disewa',
        ]);

        // 5. KURANGI STOK ALAT DI LAPANGAN
        $alat->decrement('stok_tersedia');

        return redirect()->route('sewa.index')->with('success', 'Transaksi penyewaan berhasil dicatat! Stok alat otomatis berkurang.');
    }

    /**
     * FORM PENGEMBALIAN ALAT & HITUNG DENDA
     */
    public function returnPage($id)
    {
        $sewa = Sewa::with(['pelanggan', 'cabang'])->findOrFail($id);
        $tglHarusKembali = Carbon::parse($sewa->tgl_kembali);
        $tglHariIni = Carbon::now();

        // Hitung apakah ada keterlambatan hari
        $terlambatHari = 0;
        $totalDenda = 0;
        $tarifDendaPerHari = 20000; // Contoh tarif denda flat Rp20.000/hari jika telat

        if ($tglHariIni->gt($tglHarusKembali)) {
            $terlambatHari = $tglHarusKembali->diffInDays($tglHariIni);
            $totalDenda = $terlambatHari * $tarifDendaPerHari;
        }

        return view('sewa.return', compact('sewa', 'terlambatHari', 'totalDenda', 'tglHariIni'));
    }

    /**
     * PROSES PENGEMBALIAN & KEMBALIKAN STOK ALAT
     */
    public function processReturn(Request $request, $id)
    {
        $sewa = Sewa::findOrFail($id);
        
        // 1. Update status sewa menjadi Selesai dan simpan denda jika ada
        $sewa->update([
            'status' => 'Selesai',
            'total_denda' => $request->total_denda ?? 0 // Pastikan kolom ini ada/didukung di struktur Anda
        ]);

        // 2. KEMBALIKAN STOK ALAT KE GUDANG CABANG
        // (Asumsi di sistem nyata: Anda melacak id_alat yang disewa, misal lewat tabel sewa atau pivot)
        // Jika model Sewa Anda langsung memiliki id_alat, kita panggil relasinya:
        if(isset($sewa->id_alat)) {
            Alat::where('id', $sewa->id_alat)->increment('stok_tersedia');
        }

        return redirect()->route('sewa.index')->with('success', 'Proses pengembalian alat sukses! Stok gudang telah dipulihkan.');
    }
}