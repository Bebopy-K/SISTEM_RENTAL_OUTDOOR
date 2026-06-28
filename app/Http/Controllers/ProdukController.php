<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::orderBy('nama_produk')->get();
        return view('produk.index', compact('produks'));
    }

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|string|max:20|unique:produk,kode_produk',
            'nama_produk' => 'required|string|max:200',
            'harga_sewa' => 'required|integer|min:0',
        ]);

        Produk::create($request->all());

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return view('produk.edit', compact('produk'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $request->validate([
            'kode_produk' => 'required|string|max:20|unique:produk,kode_produk,' . $id . ',id_produk',
            'nama_produk' => 'required|string|max:200',
            'harga_sewa' => 'required|integer|min:0',
        ]);

        $produk->update($request->all());

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        // Cek apakah produk memiliki transaksi
        if ($produk->transaksi()->exists()) {
            return redirect()->route('produk.index')
                ->with('error', 'Produk tidak dapat dihapus karena masih memiliki transaksi.');
        }
        $produk->delete();
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}