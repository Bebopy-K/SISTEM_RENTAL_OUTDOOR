<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::orderBy('nama_kota')->get();
        return view('cabang.index', compact('cabangs'));
    }

    public function create()
    {
        return view('cabang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:cabang,kode_cabang',
            'nama_kota' => 'required|string|max:100',
        ]);

        Cabang::create($request->all());

        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $cabang = Cabang::findOrFail($id);
        return view('cabang.edit', compact('cabang'));
    }

    public function update(Request $request, $id)
    {
        $cabang = Cabang::findOrFail($id);
        $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:cabang,kode_cabang,' . $id . ',id_cabang',
            'nama_kota' => 'required|string|max:100',
        ]);

        $cabang->update($request->all());

        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $cabang = Cabang::findOrFail($id);

        if ($cabang->transaksi()->exists()) {
            return redirect()->route('cabang.index')
                ->with('error', 'Cabang tidak dapat dihapus karena masih memiliki transaksi.');
        }

        $cabang->delete();
        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}