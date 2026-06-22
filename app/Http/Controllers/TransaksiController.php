<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Produk;

class TransaksiController extends Controller
{
    // 1. Menampilkan Semua Riwayat Transaksi (Halaman Index)
    public function index()
    {
        $user = Auth::user();
        
        // Pembatasan data berdasarkan hak akses Cabang (RBAC)
        if ($user->role === 'admin_cabang') {
            $transaksis = Transaksi::with(['produk', 'cabang'])
                ->where('cabang_id', $user->cabang_id)
                ->orderBy('id_transaksi', 'desc')
                ->get();
        } else {
            $transaksis = Transaksi::with(['produk', 'cabang'])
                ->orderBy('id_transaksi', 'desc')
                ->get();
        }
        
        return view('transaksi.index', compact('transaksis'));
    }

    // 2. Menampilkan Form Tambah Transaksi Multi-Item
    public function create()
    {
        $produks = Produk::all();
        return view('transaksi.create', compact('produks'));
    }

    // 3. Menyimpan Data Transaksi Baru (Bisa Banyak Barang Sekaligus)
    public function store(Request $request)
    {
        $request->validate([
            'produk_id'   => 'required|array',
            'produk_id.*' => 'required|exists:produk,id_produk',
            'jumlah'      => 'required|array',
            'jumlah.*'    => 'required|integer|min:1',
            'durasi'      => 'required|array',
            'durasi.*'    => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $cabangId = $user->cabang_id ?? 1; 
        $tanggalHariIni = now()->toDateString();

        foreach ($request->produk_id as $index => $prodId) {
            $produk = Produk::find($prodId);
            $qty = $request->jumlah[$index];
            $days = $request->durasi[$index];
            
            // Hitung finansial kotor otomatis
            $totalHarga = $qty * $days * $produk->harga_sewa;

            Transaksi::create([
                'tanggal'     => $tanggalHariIni,
                'produk_id'   => $prodId,
                'jumlah'      => $qty,
                'durasi'      => $days,
                'cabang_id'   => $cabangId,
                'user_id'     => $user->id_user,
                'total_harga' => $totalHarga,
                'denda'       => 0
            ]);
        }

        return redirect()->route('transaksi.index')->with('success', 'Semua item transaksi berhasil dicatat!');
    }

    // 4. Menampilkan Form Edit Transaksi Tunggal (Menyelesaikan Error Method Does Not Exist)
    public function edit($id)
    {
        // Mencari data transaksi berdasarkan ID primary key aslinya
        $transaksi = Transaksi::where('id_transaksi', $id)->firstOrFail();
        $produks = Produk::all();
        
        return view('transaksi.edit', compact('transaksi', 'produks'));
    }

    // 5. Memproses Pembaharuan Data Transaksi & Hitung Uang Ulang
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id_produk',
            'jumlah'    => 'required|integer|min:1',
            'durasi'    => 'required|integer|min:1',
            'denda'     => 'required|integer|min:0',
        ]);

        $transaksi = Transaksi::where('id_transaksi', $id)->firstOrFail();
        $produk = Produk::find($request->produk_id);

        // Kalkulasi ulang total harga item pasca perubahan kuantitas
        $totalHarga = $request->jumlah * $request->durasi * $produk->harga_sewa;

        $transaksi->update([
            'produk_id'   => $request->produk_id,
            'jumlah'      => $request->jumlah,
            'durasi'      => $request->durasi,
            'total_harga' => $totalHarga,
            'denda'       => $request->denda,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Data transaksi berhasil diperbarui!');
    }

    // 6. Memproses Penghapusan Catatan Transaksi
    public function destroy($id)
    {
        $transaksi = Transaksi::where('id_transaksi', $id)->firstOrFail();
        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Data transaksi berhasil dihapus!');
    }
}