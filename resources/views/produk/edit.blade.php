@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Edit Produk: {{ $produk->nama_produk }}</h1>
            <p class="text-muted">Ubah data produk yang sudah ada.</p>
        </div>
        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('produk.update', $produk->id_produk) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="kode_produk" class="form-label fw-bold">Kode Produk <span class="text-danger">*</span></label>
                    <input type="text" name="kode_produk" id="kode_produk" class="form-control @error('kode_produk') is-invalid @enderror" value="{{ old('kode_produk', $produk->kode_produk) }}" required>
                    @error('kode_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nama_produk" class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_produk" id="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
                    @error('nama_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="harga_sewa" class="form-label fw-bold">Harga Sewa (per hari) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_sewa" id="harga_sewa" class="form-control @error('harga_sewa') is-invalid @enderror" value="{{ old('harga_sewa', $produk->harga_sewa) }}" required>
                    @error('harga_sewa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Produk
                    </button>
                    <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection