<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Produk;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Transaksi::with(['produk', 'cabang', 'user']);

        // =============================================
        // FILTER CABANG BERDASARKAN ROLE
        // =============================================
        // Manager dan Staff hanya melihat cabangnya sendiri
        if (in_array($user->role, ['manager', 'staff'])) {
            $query->where('cabang_id', $user->cabang_id);
        }
        // Superadmin bisa melihat semua (tidak difilter otomatis)

        // Fitur Pencarian (berdasarkan ID atau Nama Produk)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_transaksi', 'LIKE', "%{$search}%")
                  ->orWhereHas('produk', function ($sub) use ($search) {
                      $sub->where('nama_produk', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id_transaksi');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $transaksis = $query->paginate(10)->appends($request->query());

        return view('transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('transaksi.create', compact('produks'));
    }

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

        // Manager/Staff: ambil cabang dari user
        // Superadmin: jika tidak punya cabang, fallback ke 1
        $cabangId = $user->cabang_id ?? 1;
        $tanggalHariIni = now()->toDateString();
        $denda = $request->denda ?? 0;

        foreach ($request->produk_id as $index => $prodId) {
            $produk = Produk::find($prodId);
            $qty = $request->jumlah[$index];
            $days = $request->durasi[$index];
            $totalHarga = $qty * $days * $produk->harga_sewa;

            Transaksi::create([
                'tanggal'     => $tanggalHariIni,
                'produk_id'   => $prodId,
                'jumlah'      => $qty,
                'durasi'      => $days,
                'cabang_id'   => $cabangId,
                'user_id'     => $user->id_user,
                'total_harga' => $totalHarga,
                'denda'       => $denda,
            ]);
        }

        return redirect()->route('transaksi.index')->with('success', 'Semua item transaksi berhasil dicatat!');
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['produk', 'cabang', 'user'])
            ->where('id_transaksi', $id)
            ->firstOrFail();
        return view('transaksi.show', compact('transaksi'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        // Staff tidak diizinkan mengedit
        if ($user->role === 'staff') {
            abort(403, 'Staff tidak diizinkan mengedit transaksi.');
        }

        $transaksi = Transaksi::where('id_transaksi', $id)->firstOrFail();

        // Manager hanya bisa edit transaksi di cabangnya
        if ($user->role === 'manager' && $transaksi->cabang_id !== $user->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi cabang lain.');
        }

        $produks = Produk::all();
        return view('transaksi.edit', compact('transaksi', 'produks'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        // Staff tidak diizinkan mengupdate
        if ($user->role === 'staff') {
            abort(403, 'Staff tidak diizinkan mengupdate transaksi.');
        }

        $transaksi = Transaksi::where('id_transaksi', $id)->firstOrFail();

        // Manager hanya bisa update transaksi di cabangnya
        if ($user->role === 'manager' && $transaksi->cabang_id !== $user->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi cabang lain.');
        }

        $request->validate([
            'produk_id' => 'required|exists:produk,id_produk',
            'jumlah'    => 'required|integer|min:1',
            'durasi'    => 'required|integer|min:1',
            'denda'     => 'required|integer|min:0',
        ]);

        $produk = Produk::find($request->produk_id);
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

    public function destroy($id)
    {
        $user = Auth::user();
        // Staff tidak diizinkan menghapus
        if ($user->role === 'staff') {
            abort(403, 'Staff tidak diizinkan menghapus transaksi.');
        }

        $transaksi = Transaksi::where('id_transaksi', $id)->firstOrFail();

        // Manager hanya bisa hapus transaksi di cabangnya
        if ($user->role === 'manager' && $transaksi->cabang_id !== $user->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi cabang lain.');
        }

        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Data transaksi berhasil dihapus!');
    }
}