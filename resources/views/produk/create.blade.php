@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Tambah Produk Baru</h1>
            <p class="text-muted">Isi data produk alat outdoor yang akan disewakan.</p>
        </div>
        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('produk.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="kode_produk" class="form-label fw-bold">Kode Produk <span class="text-danger">*</span></label>
                    <input type="text" name="kode_produk" id="kode_produk" class="form-control @error('kode_produk') is-invalid @enderror" value="{{ old('kode_produk') }}" placeholder="Contoh: TND01" required>
                    @error('kode_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nama_produk" class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_produk" id="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk') }}" placeholder="Contoh: Tenda Dome 4P" required>
                    @error('nama_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="harga_sewa" class="form-label fw-bold">Harga Sewa (per hari) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_sewa" id="harga_sewa" class="form-control @error('harga_sewa') is-invalid @enderror" value="{{ old('harga_sewa') }}" placeholder="Contoh: 50000" required>
                    @error('harga_sewa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                    <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection